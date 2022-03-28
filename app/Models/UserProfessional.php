<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfessional extends Model
{
    protected $fillable = ['is_professional_experience' , 'professional_experience' , 'is_college_degree' , 'college_degree' , 'user_id'];

}
