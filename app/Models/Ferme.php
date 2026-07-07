<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ferme extends Model
{
    protected $fillable = [
        'nom',
        'idsite',
        'longitude',
        'latitude',
        'longueur',
        'largeur',
        'gerant',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class, 'idsite');
    }

    public function gerantUser()
    {
        return $this->belongsTo(User::class, 'gerant');
    }
}
