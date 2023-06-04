<?php

namespace App\Service\Handling\Handlers\HandlingResult;

class FailedHandlingResult implements HandlingResultInterface
{
    public function __construct(public string $errorMessage)
    {

    }
}