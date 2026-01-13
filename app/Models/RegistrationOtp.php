<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'encrypted_password',
        'otp_code',
        'otp_expires_at',
        'attempts',
        'session_token',
        'verified_at',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];
}
