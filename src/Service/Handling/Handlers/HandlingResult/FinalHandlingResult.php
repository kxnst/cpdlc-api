<?php

namespace App\Service\Handling\Handlers\HandlingResult;

class FinalHandlingResult implements HandlingResultInterface
{
    public function __construct(private HandlingResultInterface $result)
    {
    }

    public function getResult(): HandlingResultInterface
    {
        return $this->result;
    }

    public function setResult(HandlingResultInterface $result): void
    {
        $this->result = $result;
    }
}