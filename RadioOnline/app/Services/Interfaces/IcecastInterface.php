<?php

namespace App\Services\Interfaces;

interface IcecastInterface
{
    public function getStats(): array;

    public function getListeners(string $mount): array;
}
