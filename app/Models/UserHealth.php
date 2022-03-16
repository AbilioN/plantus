<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHealth extends Model
{
    protected $fillable = ['is_allergy' , 'allergy_description' , 'use_medicine' , 'medicine_description' , 'blood_type' , 'sus_card' , 'emergency_phone_number_a' , 'emergency_contact_name_a' , 'emergency_kinship_a' , 'emergency_phone_number_b' , 'emergency_contact_name_b' , 'emergency_kinship_b'];


    
}
