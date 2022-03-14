<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BasicUserMiddleware;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\UserAddress;
use App\Repositories\DocumentRepository;
use App\Repositories\UserAddressRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    private DocumentRepository $uploader;
    private DocumentCategory $documentCategory;

    public function __construct(DocumentRepository $uploader ,DocumentCategory $documentCategory)
    {   
        $this->uploader = $uploader;
        $this->documentCategory = $documentCategory->where('category' ,  'address')->first();

    }

    public function update(Request $request)
    {
        
        $data = $request->all();

        $file = $data['file'];
        unset($data['file']);
        $document = $this->updateUserAddressFile($file);


        $user = Auth::user();
        $address =  UserAddress::create([
                'user_id' => $user->id,
                'street' => $data['street'],
                'number' => $data['number'],
                'cep' =>  $data['cep'],
                'neighborhood' =>$data['neighborhood'],
                'state' => $data['state'],
                'city' => $data['city'],
                'adjunct' => $data['adjunct'],
        ]);

        $url = $this->uploader->getFileUrl($document['path']);
        $address['file_url'] = $url;
        return response()->json($address, 200);
        
    }

    public function updateUserAddressFile($file)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $user = Auth::user();
        $bucket =  'address/'.$user->id;

        
        $filePath = $this->uploader->uploadDocument($file , $bucket);

        
        $createdDocument =  Document::create([
            'path' => $filePath,
            'extension' => $fileExtension,
            'document_category_id' => $this->documentCategory->id,
            'user_id' => $user->id,

        ]);

        if($createdDocument)
        { 
            
            return $createdDocument;
        }
    }
}
