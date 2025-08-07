<?php

namespace App\Services\Interfaces;

interface LiquidSoapServiceInterface
{
    public function skip(): void;

    public function shutdown(): void;

    public function restart(): void;

    public function memory_purge(): void;
}
