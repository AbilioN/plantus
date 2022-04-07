<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\UserDocuments;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\DocumentCategory;
use App\Models\UserAmericanVisa;
use App\Models\UserDocumentData;
use App\Models\UserPassportCard;
use App\Models\UserVoteCard;
use App\Models\UserWorkCard;
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
    
        // $data = $request->all();
        // dd($data);
        // $this->document = [
        //     'document' => [
        //         'rg' => '2497357',
        //         'date_emission' =>'02-04-2006',
        //         'issuing_agency'  => 'ITEP',
        //         'issuing_state' => 'RN',
        //         'file' => $data['file1']
        //     ],
        //     'work_card' => [
        //         'number' => '235554454615421',
        //         'serie' => '32563',
        //         'pis_pased' => '32653562553',
        //         'date_emission' => '01-09-1994',
        //         'file' => $data['file2']
        //     ],
        //     'vote_card' => [
        //         'number' => '653556565423233',
        //         'session' => '21',
        //         'zone' => '51',
        //         'file' => $data['file3']

        //     ],
        //     'passport' => [
        //         'passport' => '58553ABD5622',
        //         'date_emission' => '01-09-1994',
        //         'expiration_date'  => '01-09-2004',
        //         'file' => $data['file2']

        //     ],
        //     'american_visas' => [
        //         'number' => '653556565423233',
        //         'date_emission' => '01-09-1994',
        //         'expiration_date'  => '01-09-2004',
        //         'file' => $data['file2']
        //     ],
        //     'document_data' => [
        //         'gender' => 'Feminino',
        //         'marital_status' => 'Casada',
        //         'mother' => 'Maria Silva',
        //         'father' => 'João Silva',
        //         'bank_account' => 'Banco do Brasil - 001 agência 253-x - Conta Corrente 253265-1'
        //     ]
        // ];

        // $data = $this->document;

        $data = $request->all();


        if(isset($data['document']))
        {
            $userDocument = $this->updateUserDocument($data['document']);
        }

        if(isset($data['work_card']))
        {
            $userWorkCard =  $this->updateUserWorkCard($data['work_card']);
        }

        if(isset($data['vote_card']))
        {
            $userVoteCard =  $this->updateUserVoteCard($data['vote_card']);
        }

        if(isset($data['passport']))
        {
            $userPassport =  $this->updateUserPassport($data['passport']);
        }

        if(isset($data['american_visas']))
        {
            $userAmericanVisa =  $this->updateUserAmericanVisas($data['american_visas']);
        }

        if(isset($data['document_data']))
        {
            $userDocumentData = $this->updateUserDocumentData($data['document_data']);
        }
        if($userDocument && $userWorkCard && $userVoteCard && $userPassport && $userAmericanVisa && $userDocumentData)
        {
            return response()->json(['success' => true], 200);
        }else{
            dd('errou');
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
                $userDocument->user_id  = $this->user->id;

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

            if($userDocument->save() && $updatedDocument->save()) {
                DB::commit();
                // return response()->json(['success' => true] , 200);
                return true;
            }

        }else {
            return response()->json(['error' => $validator->errors()], 500);
        }

    }

    private function updateUserDocumentFile($userDocumentFile)
    {   

        $bucket =  'user/document/'.$this->user->id;
        $fileExtension = $userDocumentFile->getClientOriginalExtension();
        
        $documentCategory = DocumentCategory::where('category', 'documents')->first();

        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' => $documentCategory->id , 'description' => 'document'])->first();
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

    private function updateUserWorkCard($workCardData)
    {
        $messages = [
            'number.required' => 'é necessário informar o número da carteira de trabalho',
            'serie.required' => 'é necessário informar a série da carteira de trabalho',
            'pis_pased.required' => 'é necessário informar o pis_pased da carteira de trabalho',
            'date_emission.required' => 'é necessário informar a data de emissão da carteira de trabalho'
        ];

        $rules = [
            'number' => 'required|string',
            'serie' => 'required|string',
            'pis_pased' => 'required|string',
            'date_emission' => 'required|string'
        ];

        $validator = Validator::make($workCardData, $rules, $messages);

        if($validator->passes())
        {
            $userWorkCard = UserWorkCard::where('user_id', $this->user->id)->first();
            if(!$userWorkCard)
            {
                $userWorkCard = UserWorkCard::make(['user_id' => $this->user->id]);
            }

            $dateEmission = new Carbon($workCardData['date_emission']);
            
            if(isset($workCardData['file']))
            {
                $updatedWorkCard = $this->updateUserWorkCardFile($workCardData['file']);
                unset($workCardData['file']);
            }

            $userWorkCard->number = $workCardData['number'];
            $userWorkCard->date_emission = $dateEmission;
            $userWorkCard->serie = $workCardData['serie'];
            $userWorkCard->pis_pased = $workCardData['pis_pased'];
            try{

                if($userWorkCard->save() && $updatedWorkCard->save()) {
                    DB::commit();
                    // return response()->json(['success' => true] , 200);
                    return true;
                }

            }catch(Exception $e){
                DB::rollback();
                // return response()->json(['error' => $e->getMessage()], 500);
                return false;
            }

        }else {
            return response()->json(['error' => $validator->errors()], 500);
        }
    }

    private function updateUserWorkCardFile($workCardFile)
    {
        $bucket =  'user/work_card/'.$this->user->id;
        $fileExtension = $workCardFile->getClientOriginalExtension();
        $documentCategory = DocumentCategory::where('category', 'work_card')->first();
        
        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$documentCategory->id , 'description' => 'work_card'])->first();
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

        $filePath =   $this->uploader->uploadDocument($workCardFile , $bucket);
        if($filePath)
        {
            $createdDocument =  Document::make([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'work_card'
            ]);
                
            return $createdDocument;
        }
    }

    private function updateUserVoteCard($userVoteCardData)
    {
        DB::beginTransaction();
        $messages = [
            'number.required' => 'é necessário informar o número do título de eleitor',
            'session.required' => 'é necessário informar a série do título de eleitor',
            'zone.required' => 'é necessário informar o pis_pased do título de eleitor',
        ];

        $rules = [
            'number' => 'required|string',
            'session' => 'required|string',
            'zone' => 'required|string',
        ];
        $validator = Validator::make($userVoteCardData, $rules, $messages);
        // dd($userVoteCard);
        if($validator->passes())
        {
            $userVoteCard = UserVoteCard::where('user_id', $this->user->id)->first();
            if(!$userVoteCard)
            {
                $userVoteCard = UserVoteCard::make(['user_id' , $this->user->id]);
                $userVoteCard->user_id  = $this->user->id;

            }
            // $dateEmission = new Carbon($userVoteCard['date_emission']);
            
            if(isset($userVoteCardData['file']))
            {
                $updatedDocument = $this->updateUserVoteCardFile($userVoteCardData['file']);
                unset($userVoteCardData['file']);
            }


            $userVoteCard->number = $userVoteCardData['number'];
            // $userVoteCard->date_emission = $dateEmission;
            $userVoteCard->session = $userVoteCardData['session'];
            $userVoteCard->zone = $userVoteCardData['zone'];

            if($userVoteCard->save() && $updatedDocument->save()) {
                DB::commit();
                // return response()->json(['success' => true] , 200);
                return true;
            }

        }else {
            // return response()->json(['error' => $validator->errors()], 500);
            return false;
        }
    }

    private function updateUserVoteCardFile($userVoteCardFile)
    {
        $bucket =  'user/passport_card/'.$this->user->id;
        $fileExtension = $userVoteCardFile->getClientOriginalExtension();
        $documentCategory = DocumentCategory::where('category' , 'vote_card')->first();
        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$documentCategory->id , 'description' => 'vote_card'])->first();
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

        $filePath =   $this->uploader->uploadDocument($userVoteCardFile , $bucket);
        if($filePath)
        {
            $createdDocument =  Document::make([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'vote_card'
            ]);
                
            return $createdDocument;
        }


    }

    private function updateUserPassport($userPassportData)
    {
        $messages = [
            'passport.required' => 'é necessário informar o passaporte',
            'date_emission.required' => 'é necessário informar data de emissão',
            'expiration_date.required' => 'é necessário a data de validade',
        ];

        $rules = [
            'passport' => 'required|string',
            'date_emission' => 'required|string',
            'expiration_date' => 'required|string',
        ];
        $validator = Validator::make($userPassportData, $rules, $messages);

        if($validator->passes())
        {
            $userPassportCard = UserPassportCard::where('user_id', $this->user->id)->first();
            if(!$userPassportCard)
            {
                $userPassportCard = UserPassportCard::make(['user_id' , $this->user->id]);
                $userPassportCard->user_id = $this->user->id;
            }
            $dateEmission = new Carbon($userPassportData['date_emission']);
            $expirationDate = new Carbon($userPassportData['expiration_date']);

            
            if(isset($userPassportData['file']))
            {
                $updatedDocument = $this->updateUserPassportFile($userPassportData['file']);
                unset($userPassportData['file']);
            }

            $userPassportCard->passport = $userPassportData['passport'];
            $userPassportCard->date_emission = $dateEmission;
            $userPassportCard->expiration_date = $expirationDate;
            try{

                if($userPassportCard->save() && $updatedDocument->save()) {
                    // dd("else");
                    // return response()->json(['success' => true] , 200);
                    return true;
                }

            }catch(Exception $e){
                // return response()->json(['error' => $e->getMessage()], 500);
                dd($e->getMessage());
                return false;
            }
        }
    }

    private function updateUserPassportFile($userPassportFile)
    {
        $bucket =  'user/passport_card/'.$this->user->id;
        $fileExtension = $userPassportFile->getClientOriginalExtension();
        
        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$this->documentCategory->id , 'description' => 'passport_card'])->first();
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

        $filePath =   $this->uploader->uploadDocument($userPassportFile , $bucket);
        if($filePath)
        {
            $createdDocument =  Document::make([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'passport_card'
            ]);
                
            return $createdDocument;
        }
    }

    private function updateUserAmericanVisas($userAmericanVisasData) {
        $messages = [
            'number.required' => 'é necessário informar o número do visto americano',
            'date_emission.required' => 'é necessário informar data de emissão',
            'expiration_date.required' => 'é necessário a data de validade',
        ];

        $rules = [
            'number' => 'required|string',
            'date_emission' => 'required|string',
            'expiration_date' => 'required|string',
        ];
        $validator = Validator::make($userAmericanVisasData, $rules, $messages);

        if($validator->passes())
        {
            $userAmericanVisa = UserAmericanVisa::where('user_id', $this->user->id)->first();
            if(!$userAmericanVisa)
            {
                $userAmericanVisa = UserAmericanVisa::make(['user_id' , $this->user->id]);
                $userAmericanVisa->user_id = $this->user->id;

            }
            $dateEmission = new Carbon($userAmericanVisasData['date_emission']);
            $expirationDate = new Carbon($userAmericanVisasData['expiration_date']);

            
            if(isset($userAmericanVisasData['file']))
            {
                $updatedDocument = $this->updateUserAmericanVisasFile($userAmericanVisasData['file']);
                unset($userAmericanVisasData['file']);
            }

            $userAmericanVisa->number = $userAmericanVisasData['number'];
            $userAmericanVisa->date_emission = $dateEmission;
            $userAmericanVisa->expiration_date = $expirationDate;

            try{

                if($userAmericanVisa->save() && $updatedDocument->save()) {

                    // return response()->json(['success' => true] , 200);
                    return true;
                }

            }catch(Exception $e){
                // return response()->json(['error' => $e->getMessage()], 500);
                // dd($e->getMessage());
                return false;
            }
        }
    }

    private function updateUserAmericanVisasFile($userAmericanVisaFile)
    {
        $bucket =  'user/american_visas/'.$this->user->id;
        $fileExtension = $userAmericanVisaFile->getClientOriginalExtension();
        
        $currentDocument = Document::where(['user_id' => $this->user->id , 'document_category_id' =>$this->documentCategory->id , 'description' => 'american_visas'])->first();
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

        $filePath =   $this->uploader->uploadDocument($userAmericanVisaFile , $bucket);
        if($filePath)
        {
            $createdDocument =  Document::make([
                'path' => $filePath,
                'extension' => $fileExtension,
                'document_category_id' => $this->documentCategory->id,
                'user_id' => $this->user->id,
                'description' => 'american_visas'
            ]);
            // dd($createdDocument);
                
            return $createdDocument;
        }
    }

    private function updateUserDocumentData($userDocumentData)
    {

        $messages = [
            'gender.required' => 'é necessário informar o gênero',
            'marital_status.required' => 'é necessário informar o estado civil',
            'mother.required' => 'é necessário informar o nome da mãe',
            'bank_account.required' => 'é necessário informar os dados bancários',

        ];

        $rules = [
            'gender' => 'required|string',
            'marital_status' => 'required|string',
            'mother' => 'required|string',
            'bank_account' => 'required|string',
        ];

        $validator = Validator::make($userDocumentData, $rules, $messages);

        
        if($validator->passes())
        {
            $userDocument = UserDocumentData::where('user_id', $this->user->id)->first();
            if(!$userDocument)
            {
                $userDocument = UserDocumentData::make();
                $userDocument->user_id = $this->user->id;

            }
            $userDocument->gender = $userDocumentData['gender'];
            $userDocument->marital_status = $userDocumentData['marital_status'];
            $userDocument->mother = $userDocumentData['mother'];
            $userDocument->bank_account = $userDocumentData['bank_account'];

            if(isset($userDocumentData['father']))
            {
                $userDocument->father = $userDocumentData['father'];

            }

            if($userDocument->save())
            {
                return true;
            }
            

        }else {
            return false;
        }
    }
}
