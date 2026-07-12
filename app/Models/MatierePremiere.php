<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MatierePremiere extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'code',
        'image',
        'unite',
        'seuil_alerte',
    ];

    protected $casts = [
        'seuil_alerte' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = $model->generateUniqueCode();
            }
        });
    }

    public function generateUniqueCode()
    {
        $prefix = strtoupper(substr($this->nom, 0, 3));
        
        do {
            $randomPart = strtoupper(Str::random(2));
            $code = $prefix . '-' . $randomPart;
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function lots()
    {
        return $this->belongsToMany(Lot::class, 'lot_matiere_premiere')
                    ->withPivot('quantite', 'quantite_utiliser')
                    ->withTimestamps();
    }
}
