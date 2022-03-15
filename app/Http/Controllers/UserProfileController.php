<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Repositories\DocumentRepository;
use App\Models\DocumentCategory;
use App\User;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    private DocumentRepository $uploader;
    private DocumentCategory $documentCategory;
    public function __construct(DocumentRepository $uploader, DocumentCategory $documentCategory)
    {
        $this->uploader = $uploader;
        $this->documentCategory = $documentCategory->where('category' , 'avatar')->first();
    }

    public function update(Request $request)
    {

        try {
            $data = $request->all();
            $avatar = $data['avatar'];
            unset($data['avatar']);
    
            $document = $this->updateUserAvatar($avatar);
    
            $user = Auth::user();
    
            $avatarUrl = $this->uploader->getFileUrl($document['path']);
    
            $data['avatar'] = $avatarUrl;
            
            $birthDate = new Carbon($data['birth_date']);

            $startPlantus = new Carbon($data['start_plantus']);


            // $birthDate = $birthDate->format('Y-m-d');
            // $birthDate = $birthDate->format('d-m-Y');

            // $insertData = [
            //     'name' => $data['name'],
            //     'birth_date' => $birthDate->format('d-m-Y'),
            //     'phone' => $data['phone'],
            //     'whatsapp' => $data['whatsapp'],
            //     'avatar' => $avatarUrl
            // ];

            $user->name = $data['name'];
            $user->birth_date = $birthDate;
            $user->start_plantus = $startPlantus;
            $user->phone = $data['phone'];
            $user->whatsapp = $data['whatsapp'];
            $user->avatar = $avatarUrl;
            try{
                // $updatedUser = User::find($user->id)->update($insertData);

                $updatedUser = $user->save();
                if($updatedUser)
                {
                    $user = Auth::user();
                    $userArray = $user->toArray();
                    $userArray['birth_date'] = $user->birth_date->format('d-m-Y');
                    return response()->json($userArray);
                }
            }catch(Exception $e)
            {
                dd($e->getMessage());
            }


        }catch (\Exception $e) {
            dd($e->getMessage());
        }


    }


    public function updateUserAvatar($avatar)
    {
        $fileExtension = $avatar->getClientOriginalExtension();
        $user = Auth::user();
        $bucket =  'user/avatar/'.$user->id;
    
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

        $filePath = $this->uploader->uploadDocument($avatar , $bucket);

        if($filePath)
        {
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

    public function find(Request $request)
    {
        $user = Auth::user();

        if(!$user)
        {
            throw new Exception('erro de autenticação de usuário');
        }

        $user = collect($user);
        $data = $user->except(['remember_token' , 'created_at' , 'updated_at']);

        return response()->json($data, 200);
    }
}
