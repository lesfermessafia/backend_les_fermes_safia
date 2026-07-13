<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Magasin extends Model
{
    use HasFactory;
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

    public function matieresPremieres()
    {
        return $this->belongsToMany(MatierePremiere::class, 'lot_matiere_premiere')
                    ->withPivot('quantite', 'quantite_utiliser')
                    ->withTimestamps();
    }
}
