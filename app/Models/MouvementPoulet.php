<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementPoulet extends Model
{
    protected $fillable = [
        'code_arrivage_poulet',
        'type',
        'quantite',
        'date_mouvement',
    ];

    public function arrivagePoulet()
    {
        return $this->belongsTo(ArrivagePoulet::class, 'code_arrivage_poulet', 'code');
    }
}
