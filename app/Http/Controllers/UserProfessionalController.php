<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidProfessionalExperienceException;
use App\Models\UserProfessional;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfessionalController extends Controller
{
    

    public function update(Request $request)
    {

        $data = $request->all();

        $user = Auth::user();
        
        $userProfessional = UserProfessional::where('user_id' , $user->id)->first();
        if(!$userProfessional)
        {
            $userProfessional = UserProfessional::make(['user_id' => $user->id]);
        }
        
        if(isset($data['is_professional_experience']))
        {
            if(!isset($data['professional_experience']))
            {
                throw new InvalidProfessionalExperienceException('missing professional experience field');
            }

            $userProfessional->is_professional_experience = $data['is_professional_experience'];
            $userProfessional->professional_experience = $data['professional_experience'];



        }

        
        if(isset($data['is_college_degree']))
        {
            if(!isset($data['college_degree']))
            {
                throw new InvalidProfessionalExperienceException('missing college degree field');
            }

            $userProfessional->is_college_degree = $data['is_college_degree'];
            $userProfessional->college_degree = $data['college_degree'];

        }

        try{
            $userProfessionalArray = $userProfessional->toArray();
            $savedUserProfessional = $userProfessional->save(); 

            if($savedUserProfessional)
            {
                return response()->json($userProfessionalArray);
            }

        }catch(Exception $e)
        {
            dd($e->getMessage());
        }
    }
}
