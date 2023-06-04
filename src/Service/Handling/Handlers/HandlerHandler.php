<?php

namespace App\Service\Handling\Handlers;

use App\Entity\Handler;
use App\Repository\HandlerRepository;
use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\FinalHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\MessageHandlingService;

class HandlerHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        if (str_starts_with($handlerValue, '@')) {
            $handlerValue = substr($handlerValue, 1);

            try {
                /** @var AbstractHandler $handler */
                $handler = $this->container->get($handlerValue);
                return $handler->handle($handlerValue, $nodeName, $data, $context);
            } catch (\Exception $e) {
                return new FailedHandlingResult('Handler ' . $handlerValue . ' not found');
            }
        } else {
            /** @var HandlerRepository $repository */
            $repository = $this->container->get(HandlerRepository::class);
            /** @var Handler|null $handler */
            $handler = $repository->findBy(['slug' => $handlerValue])[0] ?? null;
            if(!$handler) {
                return new FailedHandlingResult('Handler ' . $handlerValue . ' not found');
            }
            $context['handler'] = $handler;
            /** @var MessageHandlingService $handlingService */
            $handlingService = $this->container->get(MessageHandlingService::class);
            $handlingResult = $handlingService->handle($data, $handler->getRules(), $context);
        }

        return new FinalHandlingResult($handlingResult);
    }
}