<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class SetResponseFieldHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        return match ($handlerValue['type']) {
            'literal' => $this->handleLiteral($handlerValue, $nodeName, $data),
            'source' => $this->handleSource($handlerValue, $nodeName, $data, $context),
            'each' => $this->handleEach($handlerValue, $nodeName, $data, $context),
            default => new FailedHandlingResult('Response transforming failed'),
        };
    }

    private function handleLiteral(
        mixed  $handlerValue,
        string $nodeName,
        array  &$data,
    ): HandlingResultInterface
    {
        $this->setNodeValue($nodeName, $data, $handlerValue['value']);

        return new SuccessHandlingResult();
    }

    private function handleSource(
        mixed $handlerValue,
        string $nodeName,
        array &$data,
        array &$context
    ): HandlingResultInterface
    {
        $this->setNodeValue($nodeName, $data, $this->getNodeValue($handlerValue['path'], $context['request']));

        return new SuccessHandlingResult();
    }

    private function handleEach(
        mixed $handlerValue,
        string $nodeName,
        array &$data,
        array &$context
    ): HandlingResultInterface
    {
        $requestData = $context['request'];
        $traversable = $this->getNodeValue($handlerValue['path'], $requestData);
        $mapping = $handlerValue['mapping'];
        $result = [];
        foreach ($traversable as $value) {
            $requestData['pseudo'] = $value;
            $processedNode = [];
            foreach ($mapping as $key => $mappingPath) {
                $processedNode[$key] = $this->getNodeValue($mappingPath, $requestData);
            }
            $result[]= $processedNode;
        }

        $this->setNodeValue($nodeName, $data, $result);

        return new SuccessHandlingResult();
    }
}