<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPoulet extends Model
{
    protected $fillable = [
        'ferme_id',
        'poulet_id',
        'quantite',
        'date_entree',
        'date_sortie',
        'statut',
        'poids_moyen',
        'age_jours',
        'code_stock',
        'fournisseur',
        'notes',
    ];

    protected $casts = [
        'date_entree' => 'date',
        'date_sortie' => 'date',
        'poids_moyen' => 'decimal:2',
    ];

    public function ferme()
    {
        return $this->belongsTo(Ferme::class);
    }

    public function poulet()
    {
        return $this->belongsTo(Poulet::class);
    }

    public function historiques()
    {
        return $this->hasMany(HistoriqueStockPoulet::class);
    }

    public function getAgeActuelAttribute()
    {
        if (!$this->date_entree) {
            return $this->age_jours;
        }

        $joursEcoules = $this->date_entree->diffInDays(now());

        return ($this->age_jours ?? 0) + $joursEcoules;
    }

    public static function generateCodeStock()
    {
        do {
            $code = 'stck-poulet-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code_stock', $code)->exists());

        return $code;
    }

    public static function statutsForPoulet($poulet)
    {
        if ($poulet && $poulet->type === 'pondeuse') {
            return ['Démarrage', 'Pré-Ponte', 'Ponte Régulière', 'Réforme', 'non vendu'];
        }

        return ['demarrage', 'croissant', 'finition', 'vendu', 'non vendu'];
    }

    public static function statutsFinaux()
    {
        return ['vendu', 'Réforme', 'non vendu'];
    }
}
