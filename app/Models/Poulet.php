<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poulet extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'nom',
        'race',
        'type',
        'photo',
    ];

    public static function generateCode()
    {
        do {
            $code = 'poul-' . str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}
