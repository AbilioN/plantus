<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPassportCard extends Model
{
    protected $fillable = ['passport' , 'date_emission' , 'expiration_date' , 'user_id'];
}
