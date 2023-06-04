<?php

namespace App\Service\Handling\Handlers\HandlingResult;

use App\Entity\Session;

class SessionInitSuccessHandlingResult implements HandlingResultInterface
{
    public function __construct(public Session $session)
    {
    }
}