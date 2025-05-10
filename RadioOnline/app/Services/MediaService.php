<?php

namespace App\Services;

use App\Services\Interfaces\MediaServiceInterface;

class MediaService implements MediaServiceInterface
{
    /**
     * Create a new class instance.
     */
    private $resource = null;
    private $mimes = [
        'audio/mpeg' => 'mp3',
        'audio/ogg' => 'ogg',
        'audio/wav' => 'wav',
        'audio/x-wav' => 'wav',
        'audio/x-aiff' => 'aiff',
        'audio/flac' => 'flac',
        'video/mp4' => 'mp4',
        'video/x-msvideo' => 'avi',
        'video/x-matroska' => 'mkv',
        'video/webm' => 'webm',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];

    public function analyzeFile($file)
    {
        $id3 = new \getID3();
        $this->resource = $id3->analyze($file);
        return $this;
    }

    public function getDuration()
    {
        return (int) $this->resource['playtime_seconds'] ?? null;
    }

    public function getTitle()
    {
        return $this->resource['id3v1']['title'] ?? null;
    }

    public function getArtist()
    {
        return $this->resource['id3v1']['artist'] ?? null;
    }

    public function getAlbum()
    {
        return $this->resource['id3v1']['album'] ?? null;
    }

    public function getCoverBase64()
    {
        $info = $this->resource['comments']['picture'][0] ?? null;
        if ($info)
            return sprintf(
                'data:%s;base64,%s',
                $info['image_mime'],
                base64_encode($info['data'])
            );
        return null;
    }

    public function getCoverBinary()
    {
        if (empty($this->resource['comments']['picture'][0]))
            return null;

        return [
            'data' => $this->resource['comments']['picture'][0]['data'],
            'mime' => $this->resource['comments']['picture'][0]['image_mime'],
            'image_ext' => $this->mimes[$this->resource['comments']['picture'][0]['image_mime']] ?? null,
        ];
    }
}
