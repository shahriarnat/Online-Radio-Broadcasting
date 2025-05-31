<?php

namespace App\Services;

use App\Services\Interfaces\IcecastInterface;
use Illuminate\Support\Facades\Http;

class IcecastService implements IcecastInterface
{
    protected string $host;
    protected string $username;
    protected string $password;
    protected string $alias;

    public function __construct()
    {
        $this->host = config('icecast.host');
        $this->username = config('icecast.username');
        $this->password = config('icecast.password');
        $this->alias = config('icecast.alias');
    }

    /**
     * Get Icecast Server Stats
     *
     * @return array
     */
    public function getStats(): array
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->get("{$this->host}{$this->alias}/stats");

        if ($response->successful()) {
            return $this->xmlToArray($response->body());
        }

        throw new \Exception('Failed to fetch Icecast stats');
    }

    /**
     * Get Listeners for Specific Mount Point
     *
     * @param string $mount
     * @return array
     */
    public function getListeners(string $mount): array
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->get("{$this->host}{$this->alias}/listclients", [
                'mount' => '/' . $mount
            ]);

        if ($response->successful()) {
            return $this->xmlToArray($response->body());
        }

        throw new \Exception("Failed to fetch listeners for mount: {$mount}");
    }

    /**
     * Convert XML String to Array
     *
     * @param string $xmlString
     * @return array
     */
    protected function xmlToArray(string $xmlString): array
    {
        $xml = new \SimpleXMLElement($xmlString);

        // Convert to JSON then decode
        $json = json_encode($xml);
        return json_decode($json, true);
    }
}
