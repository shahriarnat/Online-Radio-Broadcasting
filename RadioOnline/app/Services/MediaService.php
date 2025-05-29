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
        return isset($this->resource['playtime_seconds']) ? intval($this->resource['playtime_seconds']) : null;
    }

    public function getTitle()
    {
        return isset($this->resource['id3v1']['title'])
            ? mb_convert_encoding($this->resource['id3v1']['title'], 'UTF-8', 'UTF-8')
            : null;
    }

    public function getArtist()
    {
        return isset($this->resource['id3v1']['artist'])
            ? mb_convert_encoding($this->resource['id3v1']['artist'], 'UTF-8', 'UTF-8')
            : null;
    }

    public function getAlbum()
    {
        return isset($this->resource['id3v1']['album'])
            ? mb_convert_encoding($this->resource['id3v1']['album'], 'UTF-8', 'UTF-8')
            : null;
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
        if (!empty($this->resource['id3v2']['comments']['picture'][0])) {
            return [
                'data' => $this->resource['id3v2']['comments']['picture'][0]['data'],
                'mime' => $this->resource['id3v2']['comments']['picture'][0]['image_mime'],
                'image_ext' => $this->mimes[$this->resource['id3v2']['comments']['picture'][0]['image_mime']] ?? null,
            ];
        }
        if (!empty($this->resource['id3v2']['APIC'][0]['data'])) {
            return [
                'data' => $this->resource['id3v2']['APIC'][0]['data'],
                'mime' => $this->resource['id3v2']['APIC'][0]['image_mime'],
                'image_ext' => $this->mimes[$this->resource['id3v2']['APIC'][0]['image_mime']] ?? null,
            ];
        }
        return null;
    }
}
