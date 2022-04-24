<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotFoundException;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\UserHealth;
use App\Repositories\DocumentRepository;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserHealthController extends Controller
{

    private DocumentRepository $uploader;
    private DocumentCategory $documentCategory;

    private User $user;
    public function __construct(DocumentRepository $uploader, DocumentCategory $documentCategory)
    {
        $this->uploader = $uploader;
        $this->documentCategory = $documentCategory->where('category' , 'health')->first();
    }


    public function find(Request $request, $user_id = null)
    {

        if($user_id)
        {
            $this->user = User::find($user_id);
        }else{
            $this->user = Auth::user();
        }

        if(!$this->user)
        {
            
            throw new UserNotFoundException();
        }


        $userHealth = UserHealth::where('user_id', $this->user->id)->first();

        if(isset($userHealth->sus_card))
        {
            $susCardDocument = Document::where(['user_id'  => $user->id , 'document_category_id' => $this->documentCategory->id , 'description' => 'sus_card'])->first();
        
           if($susCardDocument)
           {
                $susCardFileUrl = $this->uploader->getFileUrl($susCardDocument['path']);

                $susCardArray = [
                    'extension' => $susCardDocument['extension'],
                    'url' => $susCardFileUrl
                ];

           }
        }

        $vaccineCard = Document::where(['user_id'  => $this->user->id , 'document_category_id' => $this->documentCategory->id , 'description' => 'vaccine_card'])->first();
        if($vaccineCard)
        {
            
            $vaccineCardFileUrl = $this->uploader->getFileUrl($vaccineCard['path']);

            $vaccineCardArray = [
                'extension' => $vaccineCard['extension'],
                'url' => $vaccineCardFileUrl
            ];
        }
        $userHealth = collect($userHealth);
        $userHealthArray = $userHealth->toArray();

        if($susCardArray)
        {
            $userHealthArray['sus_card_file'] = $susCardArray;
        }

        if($vaccineCardArray)
        {
            $userHealthArray['vaccine_card_file'] = $vaccineCardArray;
        }

        return response()->json($userHealthArray, 200);
    }

    public function update(Request $request, $user_id = null)
    {


        if($user_id)
        {
            $this->user = User::find($user_id);
        }else{
            $this->user = Auth::user();
        }

        if(!$this->user)
        {
            
            throw new UserNotFoundException();
        }
        $data = $request->all();
        // separar os arquivos do request

        $susCardFile = $data['sus_card_file'];
        unset($data['sus_card_file']);
        $vaccineFile =  $data['vaccine_file'];
        unset($data['vaccine_file']);

        $documentSusCard = $this->updateSusCardFile($susCardFile);
        $documentVaccine = $this->updateVaccineFile($vaccineFile);

        if($documentSusCard && $documentVaccine)
        {
            $userHealth = UserHealth::where('user_id' , $this->user->id)->first();
            if(!$userHealth)
            {
                $userHealth = UserHealth::make(['user_id' , $this->user->id]);

            }

            $userHealth->user_id = $this->user->id;

            $userHealth->is_allergy = (bool) $data['is_allergy'];

            if($userHealth->is_allergy   && !empty($data['allergy_description']))
            {
                    $userHealth->allergy_description = $data['allergy_description'];
            }else {
                throw new Exception('Usuário que possui alergia precisa descrever a alergia');
            }

            $userHealth->use_medicine = (bool) $data['use_medicine'];

            if($userHealth->use_medicine   && !empty($data['medicine_description']))
            {
                    $userHealth->medicine_description = $data['medicine_description'];
            }else {
                throw new Exception('Usuário que possui doença precisa descrever a doença');
            }

            if(isset($data['blood_type']))
            {
                $userHealth->blood_type = $data['blood_type'];
            }
            if(isset($data['sus_card']))
            {
                $userHealth->sus_card = $data['sus_card'];
            }

            if(isset($data['emergency_phone_number_a']))
            {
                $userHealth->emergency_phone_number_a = $data['emergency_phone_number_a'];
            }

            if(isset($data['sus_card']))
            {
                $userHealth->sus_card = $data['sus_card'];
            }

            if(isset($data['emergency_contact_name_a']))
            {
                $userHealth->emergency_contact_name_a = $data['emergency_contact_name_a'];
            }

            if(isset($data['emergency_kinship_a']))
            {
                $userHealth->emergency_kinship_a = $data['emergency_kinship_a'];
            }

            if(isset($data['emergency_phone_number_b']))
            {
                $userHealth->emergency_phone_number_b = $data['emergency_phone_number_b'];
            }

            if(isset($data['emergency_contact_name_b']))
            {
                $userHealth->emergency_contact_name_b = $data['emergency_contact_name_b'];
            }

            if(isset($data['emergency_kinship_b']))
            {
                $userHealth->emergency_kinship_b = $data['emergency_kinship_b'];
            }

           if($userHealth->save()){

                return response()->json(['success' => true] , 200);
           }

        }

    }

    public function updateSusCardFile($susCardFile)
    {
        $fileExtension = $susCardFile->getClientOriginalExtension();
        $bucket =  'user/health/'.$this->user->id;
        

        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$this->documentCategory->id , 'description' => 'sus_card'])->first();
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
        

        $filePath = $this->uploader->uploadDocument($susCardFile , $bucket);

        if($filePath)
        {
            $createdDocument =  Document::create([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'sus_card'
            ]);

            if($createdDocument)
            { 
                
                return $createdDocument;
            }
        }

    }

    public function updateVaccineFile($vaccineFile)
    {
        $fileExtension = $vaccineFile->getClientOriginalExtension();
        $bucket =  'user/health/'.$this->user->id;
        

        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$this->documentCategory->id , 'description' => 'vaccine_card'])->first();
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
        

        $filePath = $this->uploader->uploadDocument($vaccineFile , $bucket);

        if($filePath)
        {
            $createdDocument =  Document::create([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'vaccine_card'
            ]);

            if($createdDocument)
            { 
                
                return $createdDocument;
            }
        }

    }
}
