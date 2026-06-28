<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'instagram_link',
        'facebook_link',
        'linkedin_link',
        'logo',
    ];
}
