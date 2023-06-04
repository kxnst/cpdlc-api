<?php

namespace App\Service\Handling\Exceptions;

use App\Service\Handling\Handlers\HandlingResult\FailedHandlingResult;

class HandlingException extends \Exception
{
    public function __construct(public FailedHandlingResult $result)
    {
        parent::__construct($this->result->errorMessage);
    }
}