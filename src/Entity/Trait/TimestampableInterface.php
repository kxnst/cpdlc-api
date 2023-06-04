<?php

namespace App\Entity\Trait;

use DateTime;

interface TimestampableInterface
{
    public function getCreatedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;
}