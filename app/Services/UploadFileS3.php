<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadFileS3 
{

    private Storage $storage;

    public function __construct( Storage $storage)
    {
        $this->storage = $storage;
    }

    public function uploadFile($file)
    {
        
    }
}