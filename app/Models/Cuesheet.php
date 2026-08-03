<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuesheet extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'turn',
        'notes',
        'distance',
    ];
}
