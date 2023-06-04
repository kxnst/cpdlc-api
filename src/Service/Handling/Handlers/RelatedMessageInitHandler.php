<?php

namespace App\Service\Handling\Handlers;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class RelatedMessageInitHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $identifier = $this->getNodeValue($handlerValue, $data);
        if (!$identifier) {
            return new FailedHandlingResult('Related message not found');
        }

        /** @var MessageRepository $repository */
        $repository = $this->container->get(MessageRepository::class);
        $session = $context['session'];
        /** @var Message $message */
        $message = $repository->findBy(['messageIdentifier' => $identifier, 'session' => $session])[0] ?? null;

        if (!$message) {
            return new FailedHandlingResult('Related message not found');
        }

        $context['relatedMessage'] = $message;
        $data['relatedMessage'] = $message->toArray();

        return new SuccessHandlingResult();
    }
}