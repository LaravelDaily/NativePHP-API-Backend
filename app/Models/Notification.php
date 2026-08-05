<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'DateSent',
        'Message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'DateSent' => 'datetime',
        ];
    }
}
