<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aliment extends Model
{
    use HasFactory;
    protected $fillable = [
        'nom',
        'code',
        'photo',
    ];

    public static function generateCode()
    {
        do {
            $code = 'al-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}
