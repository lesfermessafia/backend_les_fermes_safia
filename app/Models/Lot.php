<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'code_lot',
        'magasin_id',
    ];

    public function matierePremieres()
    {
        return $this->belongsToMany(MatierePremiere::class, 'lot_matiere_premiere', 'lot_id', 'matiere_premiere_id')
                    ->withPivot('quantite')
                    ->withTimestamps();
    }

    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public static function generateCodeLot()
    {
        $date = now()->format('dmy');
        $iteration = 1;
        
        do {
            $code = $date . '-' . str_pad($iteration, 2, '0', STR_PAD_LEFT);
            $iteration++;
        } while (self::where('code_lot', $code)->exists());
        
        return $code;
    }
}
