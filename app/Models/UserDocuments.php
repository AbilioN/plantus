<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocuments extends Model
{
    //
    protected $fillable = ['rg' , 'date_emission' , 'issuing_agency' , 'issuing_state' , 'user_id'];
}
