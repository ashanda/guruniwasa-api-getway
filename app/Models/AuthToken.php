<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthToken extends Model
{
      protected $fillable = [
        'access_token', 
        'expires_at',
    ];

     
}
