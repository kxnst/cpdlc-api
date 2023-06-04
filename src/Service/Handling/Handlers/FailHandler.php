<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use JetBrains\PhpStorm\Pure;

class FailHandler extends AbstractHandler
{
    #[Pure]
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        return new FailedHandlingResult($handlerValue);
    }
}