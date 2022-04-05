<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVoteCard extends Model
{
    protected $fillable = ['number' , 'session' , 'zone' , 'user_id'];
}
