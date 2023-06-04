<?php

namespace App\Service\Handling;

use App\Entity\Handler;
use App\Entity\Message;
use App\Service\Handling\Exceptions\HandlingException;
use App\Service\Handling\Handlers\AbstractHandler;
use App\Service\Handling\Handlers\AllowedHandler;
use App\Service\Handling\Handlers\AuthHandler;
use App\Service\Handling\Handlers\EmptyHandler;
use App\Service\Handling\Handlers\EachHandler;
use App\Service\Handling\Handlers\FailHandler;
use App\Service\Handling\Handlers\HandlerHandler;
use App\Service\Handling\Handlers\HandlingResult\CompletedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\FinalHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;
use App\Service\Handling\Handlers\MatchHandler;
use App\Service\Handling\Handlers\RegexHandler;
use App\Service\Handling\Handlers\RelatedMessageInitHandler;
use App\Service\Handling\Handlers\RequiredHandler;
use App\Service\Handling\Handlers\SessionInitHandler;
use App\Service\Handling\Handlers\SetResponseFieldHandler;
use App\Service\Handling\Handlers\TypeHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageHandlingService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public const HANDLERS = [
        'required' => RequiredHandler::class,
        'type' => TypeHandler::class,
        'match' => MatchHandler::class,
        'each' => EachHandler::class,
        'handler' => HandlerHandler::class,
        'regex' => RegexHandler::class,
        'auth' => AuthHandler::class,
        'allowed' => AllowedHandler::class,
        'fail' => FailHandler::class,
        'relatedMessageIdPath' => RelatedMessageInitHandler::class,
        'clientName' => EmptyHandler::class,
        'initSession' => SessionInitHandler::class,
        'relatedMessageInit' => RelatedMessageInitHandler::class,
        'setResponseField' => SetResponseFieldHandler::class,
        'responseSenderIdentifier' => EmptyHandler::class,
        'responseSessionIdentifier' => EmptyHandler::class,
        'metaData' => EmptyHandler::class
    ];

    public const HANDLING_ORDER = [
        'session',
        'relatedMessage',
        'request'
    ];

    public function __construct(
        ContainerInterface $container
    )
    {
        $this->container = $container;
    }

    public function processRequest(array &$request, array $handlingSettings, array &$context): HandlingResultInterface
    {
        try {
            $result = $this->handle($request, $handlingSettings, $context);

            return $this->postEvents($result, $request, $context);
        } catch (HandlingException $exception) {
            return $exception->result;
        }
    }

    /**
     * @throws HandlingException
     */
    public function handle(array &$request, array $handlingSettings, array &$context): HandlingResultInterface
    {
        foreach ($handlingSettings['calls'] ?? [] as $handlerName => $handlerValue) {
            $callHandler = $this->getHandler($handlerName);
            $callHandler->handle($handlerValue, $handlerName, $request, $context);
        }

        foreach (self::HANDLING_ORDER as $section) {
            $handlingResult = $this->handleSection(
                $request,
                $handlingSettings[$section] ?? [],
                $context
            );

            if ($handlingResult instanceof FailedHandlingResult) {
                throw new HandlingException($handlingResult);
            } elseif ($handlingResult instanceof FinalHandlingResult) {
                return $handlingResult->getResult();
            }
        }
        return new SuccessHandlingResult();

    }

    /**
     * @throws HandlingException
     */
    public function handleSection(array &$request, array $handlingSettings, array &$context): HandlingResultInterface
    {
        foreach ($handlingSettings as $key => $value) {
            foreach ($value as $handlerPseudo => $handlerValue) {
                $handlingResult = $this->handleSingle($handlerPseudo, $key, $handlerValue, $request, $context);
                switch ($handlingResult::class) {
                    case FailedHandlingResult::class:
                        throw new HandlingException($handlingResult);
                    case FinalHandlingResult::class:
                        return $handlingResult->getResult();
                    default:
                        break;
                }
            }
        }

        return new SuccessHandlingResult();
    }

    /**
     */
    private function handleSingle(
        string $handlerPseudo,
        string $path,
        mixed $handlerValue,
        array &$request,
        array &$context
    ): HandlingResultInterface
    {
        $handler = $this->getHandler($handlerPseudo);

        return $handler->handle($handlerValue, $path, $request, $context);
    }

    /**
     * @throws HandlingException
     */
    private function postEvents(HandlingResultInterface $result, array &$data, array $context): HandlingResultInterface
    {
        if (isset($context['runPostEvents']) && !$context['runPostEvents']) {
            return $result;
        }

        $isFailed = $result instanceof FailedHandlingResult;
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        if ($isFailed) {
            $tasks = $context['onFail'] ?? [];
        } else {
            $tasks = $context['onSuccess'] ?? [];
        }

        /** @var callable $task */
        foreach ($tasks as $task) {
            $task();
        }

        if (isset($context['message'])) {
            $message = $context['message'];
            $em->persist($context['message']);
        } else {
            /** @var  Handler $handler */
            $handler = $context['handler'];

            $responseData = [];

            $context['session']->initIdentifier();

            $context['request']['session'] = $context['session']->toArray();

            $receiver = $context['receiver'] ?? '';
            $sender = $context['sender'] ?? '';

            $message = new Message();
            $message->setToIdentifier($receiver);
            $message->setFromIdentifier($sender);
            $message->setSession($context['session']);
            $message->setRequest($data);
            $message->setIsSuccess(!$isFailed);
            $message->initMessageIdentifier();

            $rules = $handler->getNormalizationRules();
            $this->handleSection($responseData, $rules, $context);

            $message->setFormattedMessage($responseData);

            $em->persist($message);
        }
        if (isset($context['related_message'])) {
            $em->persist($context['related_message']);
        }
        if (isset($context['session'])) {
            $em->persist($context['session']);
        }

        $em->flush();

        return new CompletedHandlingResult($message, $result);
    }

    private function getHandler(string $handlerName): AbstractHandler
    {
        $handlerClass = self::HANDLERS[$handlerName] ?? null;
        if (!$handlerClass) {
            throw new \InvalidArgumentException('Handler ' . $handlerName . ' not found');
        }

        /** @var AbstractHandler $handler */
        $handler = $this->container->get($handlerClass);

        $handler->setContainer($this->container);

        return $handler;
    }
}
