<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocumentData extends Model
{
    protected $fillable = ['gender' , 'marital_status' , 'mother' , 'bank_account' , 'father'];
    
}
