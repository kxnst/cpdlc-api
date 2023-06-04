<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;
use App\Service\Handling\MessageHandlingService;

class EachHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        /** @var MessageHandlingService $handlingService */
        $handlingService = $this->container->get(MessageHandlingService::class);

        $result = true;
        foreach ($this->getNodeValue($nodeName, $data) as $nodeValue) {
            $newData = $data + ['pseudo' => $nodeValue];

            $result = $result
                && !($handlingService->handleSection($newData, $handlerValue, $context) instanceof FailedHandlingResult);
        }

        return $result
            ? new SuccessHandlingResult()
            : new FailedHandlingResult('Handling ' . $nodeName . ' each failed');
    }
}