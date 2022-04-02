<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\UserDocuments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\DocumentCategory;
use App\Repositories\DocumentRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class UserDocumentController extends Controller
{
    private DocumentRepository $uploader;
    private DocumentCategory $documentCategory;

    public function __construct(DocumentRepository $uploader, DocumentCategory $documentCategory)
    {
        $this->uploader = $uploader;
        $this->documentCategory = $documentCategory->where('category' , 'documents')->first();
    }
    public function update(Request $request)
    {
        $this->user = Auth::user();
    
        $data = $request->all();
        if(isset($data['document']))
        {
            $this->updateUserDocument($data['document']);
        }
    }

    private function updateUserDocument($userDocumentData)
    {
        DB::beginTransaction();
        $messages = [
            'rg.required' => 'é necessário informar o rg',
            'date_emission.required' => 'é necessário informar data de emissão',
            'issuing_agency.required' => 'é necessário informar o orgão emissor',
            'issuing_state.required' => 'é necessário informar o estado de emissão'
        ];

        $rules = [
            'rg' => 'required|string',
            'date_emission' => 'required|string',
            'issuing_agency' => 'required|string',
            'issuing_state' => 'required|string'
        ];
        $validator = Validator::make($userDocumentData, $rules, $messages);

        if($validator->passes())
        {
            $userDocument = UserDocuments::where('user_id', $this->user->id)->first();
            if(!$userDocument)
            {
                $userDocument = UserDocuments::make(['user_id' , $this->user->id]);
            }
            $dateEmission = new Carbon($userDocumentData['date_emission']);
            
            if(isset($userDocumentData['file']))
            {
                $updatedDocument = $this->updateUserDocumentFile($userDocumentData['file']);
                unset($userDocumentData['file']);
            }

            $userDocument->rg = $userDocumentData['rg'];
            $userDocument->date_emission = $dateEmission;
            $userDocument->issuing_agency = $userDocumentData['issuing_agency'];
            $userDocument->issuing_state = $userDocumentData['issuing_state'];

            try{

                if($userDocument->save() && $updatedDocument->save()) {

                    DB::commit();
                    return response()->json(['success' => true] , 200);
                }

            }catch(Exception $e){
                DB::rollback();
                return response()->json(['error' => $e->getMessage()], 500);
            }

        }else {
            return response()->json(['error' => $validator->errors()], 500);
        }

    }

    private function updateUserDocumentFile($userDocumentFile)
    {   

        $bucket =  'user/document/'.$this->user->id;
        $fileExtension = $userDocumentFile->getClientOriginalExtension();
        
        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$this->documentCategory->id , 'description' => 'document'])->first();
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
        $filePath =   $this->uploader->uploadDocument($userDocumentFile , $bucket);
        if($filePath)
        {
            $createdDocument =  Document::make([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'document'
            ]);
                
            return $createdDocument;
        }

    }
}
