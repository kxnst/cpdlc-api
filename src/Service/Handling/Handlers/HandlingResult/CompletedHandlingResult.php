<?php

namespace App\Service\Handling\Handlers\HandlingResult;

use App\Entity\Message;

class CompletedHandlingResult implements HandlingResultInterface
{
    public function __construct(
        public Message $message,
        public ?HandlingResultInterface $result = null
    )
    {
    }
}