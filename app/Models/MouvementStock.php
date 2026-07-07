<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $fillable = [
        'magasin_id',
        'matiere_id',
        'lot_id',
        'type',
        'quantite',
        'date_mouvement',
        'gerant_id',
        'observation',
    ];

    public function magasin()
    {
        return $this->belongsTo(Magasin::class, 'magasin_id');
    }

    public function matierePremiere()
    {
        return $this->belongsTo(MatierePremiere::class, 'matiere_id');
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class, 'lot_id');
    }

    public function gerant()
    {
        return $this->belongsTo(User::class, 'gerant_id');
    }
}
