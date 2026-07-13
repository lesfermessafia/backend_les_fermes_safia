<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOeuf extends Model
{
    // Une tablette contient 30 œufs. La quantité est gérée en nombre de tablettes.
    const OEUFS_PAR_TABLETTE = 30;

    protected $fillable = [
        'code_ferme',
        'quantite',
        'date_entree',
    ];

    protected $casts = [
        'date_entree' => 'date',
    ];

    public function historiques()
    {
        return $this->hasMany(HistoriqueOeuf::class);
    }

    public function getQuantiteOeufsAttribute()
    {
        return $this->quantite * self::OEUFS_PAR_TABLETTE;
    }
}
