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

    public function update(Request $request, $user_id = null)
    {

        try{ 



            $data = $request->all();

            $file = $data['file'];
            
            unset($data['file']);
            
            
            $document = $this->updateUserAddressFile($file);
    
    
            $user = Auth::user();
            $address = UserAddress::where( ['user_id' => $user->id])->first();
            if(!$address)
            {
                $address = new UserAddress();
                $address->user_id = $user->id;
            }

            $address->street = $data['street'];
            $address->number =  $data['number'];
            $address->cep =  $data['cep'];
            $address->neighborhood = $data['neighborhood'];
            $address->state =  $data['state'];
            $address->city = $data['city'];
            $address->adjunct = $data['adjunct'];
            
            $address->save();
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
            'description' => 'address_receipt'
        ]);


        if($createdDocument)
        { 
            
            return $createdDocument;
        }
    }
}
