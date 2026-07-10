<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'adresse',
        'longitude',
        'latitude',
        'longueur',
        'largeur',
        'gerant',
    ];

    public function gerantUser()
    {
        return $this->belongsTo(User::class, 'gerant');
    }
}
