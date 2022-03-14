<?php

namespace App\Http\Controllers;

use App\Http\Middleware\BasicUserMiddleware;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\UserAddress;
use App\Repositories\DocumentRepository;
use App\Repositories\UserAddressRepository;
use Exception;
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

        try{ 
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
            $addressArray = $address->toArray();
            $addressArray['file']['url'] = $url;
            $addressArray['file']['extension'] = $document['extension'];

            return response()->json($addressArray, 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(), 500);
        }   
    }

    public function updateUserAddressFile($file)
    {
        $fileExtension = $file->getClientOriginalExtension();
        $user = Auth::user();

        $bucket =  'user/address/'.$user->id;

        $currentDocument = Document::where(['user_id' => $user->id , 'document_category_id' =>$this->documentCategory->id])->first();
        if($currentDocument)
        {
            // remover arquivo e deletar document
            $deletedFile = $this->uploader->deleteFile($currentDocument['path']);
            $deletedDocument = $currentDocument->delete();
            if(!$deletedDocument || !$deletedFile)
            {
            
                throw new Exception('erro em deletar arquivo existente');
            }
        }

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
