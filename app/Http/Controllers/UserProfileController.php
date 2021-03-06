<?php

namespace App\Http\Controllers;

use App\Exceptions\UserNotAuthenticatedException;
use App\Exceptions\UserNotFoundException;
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

    public function update(Request $request, $user_id = null)
    {
        $data = $request->all();
        $avatar = $data['avatar'];
        unset($data['avatar']);

        $document = $this->updateUserAvatar($avatar);


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

        $avatarUrl = $this->uploader->getFileUrl($document['path']);

        $data['avatar'] = $avatarUrl;
        
        $birthDate = new Carbon($data['birth_date']);

        $startPlantus = new Carbon($data['start_plantus']);

        $this->user->name = $data['name'];
        $this->user->birth_date = $birthDate;
        $this->user->start_plantus = $startPlantus;
        $this->user->phone = $data['phone'];
        $this->user->whatsapp = $data['whatsapp'];
        $this->user->avatar = $avatarUrl;

        
        try{

            $updatedUser = $this->user->save();
            if($updatedUser)
            {
                $userArray = collect($this->user)->toArray();
                $userArray['birth_date'] = $this->user->birth_date->format('d-m-Y');
                return response()->json($userArray);
            }
        }catch(Exception $e)
        {
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
                'description' => 'user_avatar'
            ]);

            if($createdDocument)
            { 
                
                return $createdDocument;
            }
        }

        
    }

    public function find(Request $request, $user_id = null)
    {
        if($user_id)
        {
            $user = User::find($user_id);
            if(!$user)
            {
                throw new UserNotFoundException('N??o foi encontrado usu??rio para este id');
            }
        }else {
            $user = Auth::user();
        }

        if(!$user)
        {
            throw new UserNotAuthenticatedException('erro de autentica????o de usu??rio');
        }

        $user = collect($user);
        $data = $user->except(['remember_token' , 'created_at' , 'updated_at']);

        return response()->json($data, 200);
    }
}
