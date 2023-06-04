<?php

namespace App\Service\Handling\Handlers;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;
use App\Service\Handling\Handlers\HandlingResult\HandlingResultInterface;
use App\Service\Handling\Handlers\HandlingResult\SuccessHandlingResult;

class AuthHandler extends AbstractHandler
{
    public function handle(mixed $handlerValue, string $nodeName, array &$data, array &$context): HandlingResultInterface
    {
        $authData = $this->getNodeValue($nodeName, $data);

        $context['onSuccess'][] = function () use ($nodeName, &$data) {
            $this->setNodeValue($nodeName, $data, 'Auth data hidden');
        };

        return $authData['login'] == $authData['password']
            ? new SuccessHandlingResult()
            : new FailedHandlingResult('Wrong auth data');
    }
}