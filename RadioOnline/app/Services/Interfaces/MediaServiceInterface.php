<?php

namespace App\Services\Interfaces;

interface MediaServiceInterface
{
    public function analyzeFile($file);

    public function getDuration();

    public function getTitle();

    public function getArtist();

    public function getAlbum();

    public function getCoverBase64();

    public function getCoverBinary();

}
