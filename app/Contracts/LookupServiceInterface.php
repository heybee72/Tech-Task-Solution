<?php

namespace App\Contracts;

interface LookupServiceInterface
{
    public function lookup(string $username, string $userId);
}