<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class HistoriqueAliment extends Model
{
    protected $fillable = [
        'stock_aliment_id',
        'gerant_id',
        'type',
        'quantite',
        'date_mouvement',
    ];

    public function stockAliment()
    {
        return $this->belongsTo(StockAliment::class);
    }

    public function gerant()
    {
        return $this->belongsTo(User::class);
    }
}
