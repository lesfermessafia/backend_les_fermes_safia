<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePasswordCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'new_password', 'used', 'expires_at'];

    protected function casts(): array
    {
        return [
            'used' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }
}
