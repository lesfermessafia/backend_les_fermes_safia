<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrivagePoulet extends Model
{
    protected $fillable = [
        'code',
        'poulet_id',
        'quantite',
        'nom_fournisseur',
        'ferme_id',
    ];

    public function poulet()
    {
        return $this->belongsTo(Poulet::class);
    }

    public function ferme()
    {
        return $this->belongsTo(Ferme::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementPoulet::class);
    }

    public static function generateCode()
    {
        do {
            $code = 'Ar-p-' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}
