<?php

namespace App\Services\Interfaces;

interface LiquidSoapInterface
{
    public function skip(string $id): void;

    public function shutdown(): void;

    public function restart(): void;

    public function memory_purge(): void;
}
