<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAliment extends Model
{
    protected $fillable = [
        'aliment_id',
        'code_stock',
        'formule_id',
        'quantite_fabriquer',
        'quantite_utiliser',
        'status',
    ];

    public function aliment()
    {
        return $this->belongsTo(Aliment::class);
    }

    public function formule()
    {
        return $this->belongsTo(Formule::class);
    }

    public function historiques()
    {
        return $this->hasMany(HistoriqueAliment::class, 'stock_aliment_id');
    }

    public static function generateCodeStock()
    {
        do {
            $code = 'stck-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code_stock', $code)->exists());
        
        return $code;
    }
}
