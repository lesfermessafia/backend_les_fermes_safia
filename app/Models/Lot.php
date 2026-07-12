<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'code_lot',
        'created_by',
    ];

    public function matierePremieres()
    {
        return $this->belongsToMany(MatierePremiere::class, 'lot_matiere_premiere')
                    ->withPivot('quantite', 'quantite_utiliser', 'magasin_id')
                    ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
