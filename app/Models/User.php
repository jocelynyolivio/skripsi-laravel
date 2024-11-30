<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // Tambahkan 'role_id' jika perlu
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id'); // Relasi ke model Role
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'doctor_id');
    }

    // Relasi ke tabel schedules (dokter memiliki jadwal)
    public function schedules()
    {
        return $this->hasMany(Schedules::class, 'doctor_id');
    }

}
