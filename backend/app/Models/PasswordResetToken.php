<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hash',
        'valid_until'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
