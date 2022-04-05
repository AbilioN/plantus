<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWorkCard extends Model
{
    protected $fillable = ['number' , 'serie' , 'pis_pased' , 'date_emission' , 'user_id'];
}
