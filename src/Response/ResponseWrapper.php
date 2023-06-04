<?php

namespace App\Response;

class ResponseWrapper
{
    public string $receiver;

    public object $response;

    public function getReceiver(): string
    {
        return $this->receiver;
    }

    public function setReceiver(string $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getResponse(): object
    {
        return $this->response;
    }

    public function setResponse(object $response): void
    {
        $this->response = $response;
    }


}