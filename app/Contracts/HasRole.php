<?php

namespace App\Contracts;

interface HasRole
{
    public function getRole(): string;
}
