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
        'role_id',
        'tempat_lahir',
        'tanggal_lahir',
        'tanggal_bergabung',
        'nomor_sip',
        'nik',
        'nomor_telepon',
        'alamat',
        'nomor_rekening',
        'deskripsi',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'is_active' => 'boolean'
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : asset('images/default-profile.png');
    }

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
