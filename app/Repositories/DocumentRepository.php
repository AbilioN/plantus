<?php

namespace App\Repositories;

use App\Models\Document;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class DocumentRepository
{

    private Storage $uploader;
    private Document $model;
    private User $user;
    public function __construct( Storage $uploader, Document $model)
    {

        $this->uploader = $uploader;
        $this->model = $model;
        // $this->user = Auth::user();

    
    }
    
    public function uploadDocument($file , $bucket)
    {
        try {
            $path = $this->uploader::put($bucket, $file);
            if($path)
            {
                return $path;
            }
        }catch(Exception $e)
        {

        }

    }

    public function getFileUrl( string $path )
    {
        $url = $this->uploader::url($path);
        return $url;
    }

    public function deleteFile(string $path)
    {
        $fileExists = $this->uploader::exists($path);
        if($fileExists)
        {
            $deleted = $this->uploader::delete($path);
            if($deleted)
            {
                return true;
            }
        }

        return true;
    }
}