<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class TypeHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $nodeValue = $this->getNodeValue($nodeName, $data);

        $result = false;

        if (str_starts_with($handlerValue, '?')) {
            $result = is_null($nodeValue);
            $handlerValue = substr($handlerValue, 1);
        }

        $result = $result || match ($handlerValue) {
                'array' => is_array($nodeValue),
                'int' => is_int($nodeValue),
                'string' => is_string($nodeValue),
                'bool' => is_bool($nodeValue),
                default => false
            };

        return $result
            ? new SuccessHandlingResult()
            : new FailedHandlingResult('Handling ' . $nodeName . ' type failed');

    }
}