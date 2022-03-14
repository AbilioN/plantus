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
            // $birthDate = $birthDate->format('Y-m-d');
            // $birthDate = $birthDate->format('d-m-Y');

            $updatedUser = User::find($user->id)->update([
                'name' => $data['name'],
                'birth_date' => $birthDate,
                'phone' => $data['phone'],
                'whatsapp' => $data['whatsapp'],
                'avatar' => $avatarUrl
            ]);

            // $updatedUser = User::find($user->id)->update($data);


            if($updatedUser)
            {
                $user = Auth::user();
                dd($user);
                return response()->json($updatedUser->toArray());
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
}
