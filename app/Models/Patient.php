<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        // Informasi Pasien
        'patient_id',
        'fname',
        'mname',
        'lname',
        'gender',
        'nik',
        'blood_type',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'marital_status',
        'family_status',
        'occupation',
        'nationality',

        // Alamat Rumah
        'home_address',
        'home_address_domisili',
        'home_RT',
        'home_RW',
        'home_kelurahan',
        'home_kecamatan',
        'home_city',
        'home_zip_code',
        'home_country',
        'home_phone',
        'home_mobile',
        'home_email',

        // Alamat Kantor (Opsional)
        'office_address',
        'office_city',
        'office_zip_code',
        'office_country',
        'office_phone',
        'office_mobile',
        'office_email',

        // Kontak Darurat
        'emergency_contact_name',
        'emergency_contact_relation',
        'emergency_contact_phone',

        // Upload Dokumen (Opsional)
        'form_data_awal',
        'informed_consent',

        // Akun & Keamanan
        'email',
        'password',

        'birthday_voucher_code',
        'birthday_voucher_used',
        'birthday_voucher_expired_at',

        'email_verified_at',
        'updated_by'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
