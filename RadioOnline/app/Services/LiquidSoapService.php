<?php

namespace App\Services;

use App\Services\Interfaces\LiquidSoapInterface;

class LiquidSoapService implements LiquidSoapInterface
{
    protected $host = 'liquidsoap';
    protected $port = 1234;
    protected $timeout = 10;
    protected $connection;

    private function connect(): bool
    {
        $this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        return $this->connection !== false;
    }

    private function sendCommand(string $command, int $waitTimeMicroseconds = 500000): string|false
    {
        if (!$this->connection) {
            return false;
        }

        fwrite($this->connection, $command . "\n");
        usleep($waitTimeMicroseconds); // Wait for response

        $response = '';
        while (!feof($this->connection)) {
            $line = fgets($this->connection, 1024);
            if ($line === false) {
                break;
            }
            $response .= $line;

            // Optionally break if prompt detected (depends on your server)
            if (str_ends_with(trim($line), '>') || str_ends_with(trim($line), '#')) {
                break;
            }
        }

        return trim($response);
    }

    private function disconnect(): void
    {
        if ($this->connection) {
            fclose($this->connection);
            $this->connection = null;
        }
    }

    private function exec(string $command): string|false
    {
        if (!$this->connect()) {
            return false;
        }

        $response = $this->sendCommand($command);

        $this->disconnect();

        return $response;
    }

    public function skip(): void
    {
        $this->exec('output.icecast.skip');
    }

    public function shutdown(): void
    {
        $this->exec('shutdown');
    }

    public function restart(): void
    {
        $this->exec('request.all');
    }

    public function memory_purge(): void
    {
        $this->exec('runtime.gc.full_major');
    }
}
