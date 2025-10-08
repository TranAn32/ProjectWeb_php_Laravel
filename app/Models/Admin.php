<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'Admin';
    protected $primaryKey = 'adminID';
    public $timestamps = false;

    protected $fillable = [
        'userName',
        'password',
        'email',
        'role',
        'createdDate',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
}
