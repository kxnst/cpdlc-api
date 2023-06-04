<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SkipHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class RequiredHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        if(!$this->ifNodeExists($nodeName, $data) && !$handlerValue) {
            return new SkipHandlingResult();
        } elseif (!$this->ifNodeExists($nodeName, $data) && $handlerValue) {
            return new FailedHandlingResult('Handling ' . $nodeName . ' required failed');
        } else {
            return new SuccessHandlingResult();
        }
    }
}
