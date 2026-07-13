<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueOeuf extends Model
{
    protected $fillable = [
        'stock_oeuf_id',
        'gerant_id',
        'type',
        'quantite',
        'date_mouvement',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
    ];

    public function stockOeuf()
    {
        return $this->belongsTo(StockOeuf::class);
    }

    public function gerant()
    {
        return $this->belongsTo(User::class);
    }
}
