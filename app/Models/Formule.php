<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formule extends Model
{
    protected $fillable = [
        'nom',
        'photo',
        'composant',
    ];

    protected $casts = [
        'composant' => 'array',
    ];
}
