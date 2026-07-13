<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriqueStockPoulet extends Model
{
    protected $fillable = [
        'stock_poulet_id',
        'type_mouvement',
        'quantite',
        'motif',
        'date_mouvement',
        'notes',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
    ];

    public function stockPoulet()
    {
        return $this->belongsTo(StockPoulet::class);
    }
}
