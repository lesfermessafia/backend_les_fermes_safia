<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOeuf extends Model
{
    protected $fillable = [
        'code_ferme',
        'quantite',
        'date_entree',
    ];

    public function historiques()
    {
        return $this->hasMany(HistoriqueOeuf::class);
    }
}
