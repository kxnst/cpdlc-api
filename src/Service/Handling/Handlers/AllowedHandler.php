<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class AllowedHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $result = in_array($this->getNodeValue($nodeName, $data), $handlerValue);

        return $result
            ? new SuccessHandlingResult()
            : new FailedHandlingResult('Handling ' . $nodeName . ' allowed failed');
    }
}