<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
    ];
}
