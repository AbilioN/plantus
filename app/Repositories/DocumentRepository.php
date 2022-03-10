<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\User;
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
        $this->user = User::find(1);

    
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
}