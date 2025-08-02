<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'nik', 'last_activity'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
    ];
    public function karyawan()
    {
        return $this->hasOne(HrdKaryawan::class,'nik', 'nik');
    }
}
