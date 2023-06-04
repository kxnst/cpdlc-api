<?php

namespace App\Service\Handling\Handlers;

use App\Entity\Session;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class SetSessionStatusHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        /** @var Session $session */
        $session = $context['session'];

        $context['onSuccess'][] = function () use($session, $handlerValue) {
            $session->setStatus($handlerValue);
        };

        return new SuccessHandlingResult();
    }
}