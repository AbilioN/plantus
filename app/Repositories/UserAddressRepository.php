<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;
use App\Models\UserAddress;
use App\Services\UploadFileS3;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAddressRepository 
{

    private UserAddress $model;
    private User $user;
    private DocumentRepository $uploader;
    private string $bucket;
    private Document $document;
    private DocumentCategory $documentCategory;

    public function __construct(UserAddress $model , DocumentRepository $uploader , Document $document , DocumentCategory $documentCategory )
    {

        dd(Auth::user());
        $this->model = $model;
        $this->uploader = $uploader;
        // $this->user = Auth::user();
        $this->user = User::where('cpf' , '10068673418')->first();
        $this->bucket = 'address/'.$this->user->id;
        $this->document = $document;
        $this->documentCategory = $documentCategory->where('category' ,  'address')->first();
    

    }

    public function updateUserAddress(array $data)
    {

        $file = $data['file'];
        unset($data['file']);
        $this->updateUserAddressFile($file);
        // dd($data);

        // dd($this->model);
        $address =  UserAddress::create([
                'user_id' => 1,
                'street' => 'rua dos salmos',
                'number' => 53,
                'cep' =>  '59123525',
                'neighborhood' =>'pajucara',
                'state' => 'RN',
                'city' => 'Natal',
                'adjunct' => 'teste',
        ]);
        if($address)
        {
            $this->model = $address;
        }

        return $this->prepareSuccessResponse();
    }

    public function prepareSuccessResponse()
    {
        $responseArray = [
            'user_id' => $this->user->id,
            'address_id' => $this->model->id,
            'document_id' => $this->document->id,
        ];

        return $responseArray;
    }


    public function updateUserAddressFile($file)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $address = $this->userAlreadyHaveAddress();
        if($address)
        {
            // @TODO, deletar  o arquivo que ja existe
        }
        $filePath = $this->uploader->uploadDocument($file , $this->bucket);
        $createdDocument = $this->document->create([
            'path' => $filePath,
            'extension' => $fileExtension,
            'document_category_id' => $this->documentCategory->id,
            'user_id' => $this->user->id,
            'description' => 'address_receipt'

        ]);
        if($createdDocument)
        {
            $this->document = $createdDocument;
        }
    }


    public function userAlreadyHaveAddress()
    {
        return  $this->model->where('user_id' , $this->user->id)->first();
    }
}