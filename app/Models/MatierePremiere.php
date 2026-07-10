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
}
