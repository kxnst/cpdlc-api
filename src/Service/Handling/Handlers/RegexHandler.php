<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class RegexHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $value = $this->getNodeValue($nodeName, $data);
        $result = preg_match($handlerValue, $value);

        return $result === 1
            ? new SuccessHandlingResult()
            : new FailedHandlingResult('Validating ' . $nodeName . ' with regex failed');
    }
}