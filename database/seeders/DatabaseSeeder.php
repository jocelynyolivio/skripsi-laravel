<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use App\Models\Category;
use App\Models\Pricelist;
use App\Models\Procedure;
use App\Models\ChartOfAccount;
use App\Models\DentalMaterial;
use Illuminate\Database\Seeder;
use App\Models\ScheduleTemplate;
use App\Models\ProcedureMaterial;
use App\Models\ProcedureType;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // sesuai data absensi excel
        $users = [
            ['name' => 'Yohany', 'email' => 'yohany@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Astri', 'email' => 'astri@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Erika', 'email' => 'erika@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Siska', 'email' => 'siska@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Marsha', 'email' => 'marsha@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Shandra', 'email' => 'shandra@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Inneke', 'email' => 'inneke@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Felicia', 'email' => 'felicia@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Nico', 'email' => 'nico@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Stefanus', 'email' => 'stefanus@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Anin', 'email' => 'anin@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Winda', 'email' => 'winda@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Cinta', 'email' => 'cinta@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Yohana', 'email' => 'yohana@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Ajeng', 'email' => 'ajeng@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Debi', 'email' => 'debi@gmail.com', 'password' => Hash::make('password'), 'role_id' => 1],
            ['name' => 'Johana', 'email' => 'johana@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Naila', 'email' => 'naila@gmail.com', 'password' => Hash::make('password'), 'role_id' => 3],
            ['name' => 'Dina', 'email' => 'dina@gmail.com', 'password' => Hash::make('password'), 'role_id' => 3],
            ['name' => 'Radin', 'email' => 'radin@gmail.com', 'password' => Hash::make('password'), 'role_id' => 3],
            ['name' => 'Sergio', 'email' => 'sergio@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],
            ['name' => 'Ayu', 'email' => 'ayu@gmail.com', 'password' => Hash::make('password'), 'role_id' => 2],

        ];
        foreach ($users as $user) {
            User::create($user);
        }

        // tambahan
        User::create([
            'name' => 'admin1',
            'email' => 'admin1@gmail.com',
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);
        User::create([
            'name' => 'dokter1',
            'email' => 'dokter1@gmail.com',
            'password' => bcrypt('dokter'),
            'role_id' => 2
        ]);
        User::create([
            'name' => 'dokter2',
            'email' => 'dokter2@gmail.com',
            'password' => bcrypt('dokter'),
            'role_id' => 2
        ]);
        User::create([
            'name' => 'dokter3',
            'email' => 'dokter3@gmail.com',
            'password' => bcrypt('dokter'),
            'role_id' => 2
        ]);
        User::create([
            'name' => 'dokter4',
            'email' => 'dokter4@gmail.com',
            'password' => bcrypt('dokter'),
            'role_id' => 2
        ]);
        User::create([
            'name' => 'manager1',
            'email' => 'manager1@gmail.com',
            'password' => bcrypt('manager'),
            'role_id' => 3
        ]);

        Role::create([
            'role_name' => 'admin'
        ]);
        Role::create([
            'role_name' => 'dokter tetap'
        ]);
        Role::create([
            'role_name' => 'dokter luar'
        ]);
        Role::create([
            'role_name' => 'manager'
        ]);

        $patients = [
            [
                'fname' => 'Jocelyn',
                // 'mname' => 'Yoli',
                'lname' => 'Yolivio',
                'email' => 'emailnyayoli@gmail.com',
                'password' => Hash::make('12345'),
                'home_mobile' => '081230333587',
            ],
            [
                'fname' => 'Pasien',
                // 'mname' => '',
                'lname' => '1',
                'email' => 'pasien1@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '1479575675367',
            ],
            [
                'fname' => 'Pasien',
                'mname' => '2',
                // 'lname' => 'Yolivio',
                'email' => 'pasien2@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '89786785',
            ],
            [
                'fname' => 'Pasien',
                'mname' => '3',
                'lname' => 'Ya 3',
                'email' => 'pasien3@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '0000000',
            ],
        ];

        foreach ($patients as $data) {
            // Generate Patient ID
            $initialLetter = strtoupper(substr($data['fname'], 0, 1));
            $lastPatient = Patient::where('patient_id', 'like', "$initialLetter%")->latest('id')->first();

            if ($lastPatient) {
                $lastNumber = (int) substr($lastPatient->patient_id, 1);
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $data['patient_id'] = $initialLetter . $newNumber;

            // Tambahkan data lainnya yang tidak boleh null
            $data['gender'] = 'Male'; // Default gender
            $data['nik'] = '1234567890123456'; // Default NIK
            $data['blood_type'] = 'O'; // Default Blood Type
            $data['place_of_birth'] = 'Jakarta'; // Default Place of Birth
            $data['date_of_birth'] = '1990-01-01'; // Default Date of Birth
            $data['nationality'] = 'Indonesian'; // Default Nationality
            $data['home_address'] = 'Jl. Default No. 1'; // Default Home Address
            $data['home_city'] = 'Jakarta'; // Default Home City
            $data['emergency_contact_name'] = 'John Doe'; // Default Emergency Contact
            $data['emergency_contact_phone'] = '081234567890'; // Default Emergency Contact Phone

            Patient::create($data);
        }

        // ScheduleTemplate::create([
        //     'doctor_id' => 23,
        //     'day_of_week' => 'Monday',
        //     'start_time' => '10:00:00',
        //     'end_time' => '17:00:00',
        //     'is_active' => true,
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now(),
        // ]);

        $schedules = [
            // SENIN
            ['doctor_id' => 10, 'day' => 'Monday', 'start' => '08:00:00', 'end' => '14:00:00'],
            ['doctor_id' => 9, 'day' => 'Monday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // SELASA
            ['doctor_id' => 22, 'day' => 'Tuesday', 'start' => '08:00:00', 'end' => '14:00:00'],
            ['doctor_id' => 7,  'day' => 'Tuesday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // RABU
            ['doctor_id' => 10, 'day' => 'Wednesday', 'start' => '08:00:00', 'end' => '14:00:00'],
            ['doctor_id' => 9,  'day' => 'Wednesday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // KAMIS
            ['doctor_id' => 8,  'day' => 'Thursday', 'start' => '08:00:00', 'end' => '14:00:00'],
            ['doctor_id' => 10, 'day' => 'Thursday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // JUMAT
            ['doctor_id' => 9,  'day' => 'Friday', 'start' => '08:00:00', 'end' => '14:00:00'],
            ['doctor_id' => 10, 'day' => 'Friday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // SABTU
            ['doctor_id' => 8,  'day' => 'Saturday', 'start' => '08:00:00', 'end' => '13:00:00'],
            ['doctor_id' => 22, 'day' => 'Saturday', 'start' => '14:00:00', 'end' => '20:00:00'],
        
            // MINGGU
            ['doctor_id' => 22, 'day' => 'Sunday', 'start' => '11:00:00', 'end' => '17:00:00'],
        ];
        
        foreach ($schedules as $s) {
            ScheduleTemplate::create([
                'doctor_id' => $s['doctor_id'],
                'day_of_week' => $s['day'],
                'start_time' => $s['start'],
                'end_time' => $s['end'],
                'is_active' => true,
            ]);
        }

        DentalMaterial::create([
            'name' => 'Resin Komposit',
            'description' => 'Bahan untuk penambalan gigi berlubang',
            'unit_type' => 'ml'
        ]);

        DentalMaterial::create([
            'name' => 'Anestesi',
            'description' => 'Cairan anestesi lokal untuk prosedur dental',
            'unit_type' => 'ml'
        ]);

        DentalMaterial::create([
            'name' => 'Cavity Liner',
            'description' => 'Bahan pelapis untuk restorasi tambalan',
            'unit_type' => 'ml'
        ]);

        DentalMaterial::create([
            'name' => 'Amalgam',
            'description' => 'Bahan tambalan yang mengandung logam',
            'unit_type' => 'ml'
        ]);

        DentalMaterial::create(['name' => 'Alkohol 70%', 'description' => 'Cairan antiseptik', 'unit_type' => 'ml']);
        DentalMaterial::create(['name' => 'Gigi Tiruan', 'description' => 'Gigi palsu', 'unit_type' => 'pcs']);
        DentalMaterial::create(['name' => 'Bubuk Gips', 'description' => 'Bahan cetakan', 'unit_type' => 'g']);

        $types = [
            ['name' => 'Diagnosa', 'description' => 'Pemeriksaan dan diagnosis kondisi gigi'],
            ['name' => 'Penambalan Gigi', 'description' => 'Perawatan untuk gigi berlubang atau rusak ringan'],
            ['name' => 'Perawatan Saluran Akar (PSA)', 'description' => 'Perawatan akar gigi yang terinfeksi atau rusak'],
            ['name' => 'Gigi Palsu Cekat', 'description' => 'Pemasangan gigi tiruan yang permanen'],
            ['name' => 'Bedah Mulut', 'description' => 'Prosedur pembedahan dalam rongga mulut'],
            ['name' => 'Gigi Anak - Anak', 'description' => 'Perawatan gigi khusus anak-anak'],
            ['name' => 'Gigi Tiruan Sebagian', 'description' => 'Pemasangan gigi palsu sebagian'],
            ['name' => 'Perawatan Gusi', 'description' => 'Perawatan penyakit gusi dan jaringan penyangga'],
            ['name' => 'Estetika', 'description' => 'Perawatan estetika dan kosmetik gigi'],
            ['name' => 'Orthodonti', 'description' => 'Perawatan merapikan posisi gigi'],
            ['name' => 'Post - Orthodonti', 'description' => 'Perawatan lanjutan setelah orthodonti'],
            ['name' => 'Orthodonti dari DRG LUAR', 'description' => 'Perawatan orthodonti dari dokter luar'],
        ];
        
        foreach ($types as $type) {
            ProcedureType::create($type);
        }        

        // Procedure::create([
        //     'name' => 'Tambal Gigi',
        //     'description' => 'Prosedur untuk menambal gigi yang berlubang menggunakan bahan tambalan seperti resin komposit.',
        // ]);

        // Procedure::create([
        //     'name' => 'Pembersihan Karang Gigi',
        //     'description' => 'Prosedur untuk membersihkan karang gigi dan plak dengan scaler.',
        // ]);

        // Procedure::create([
        //     'name' => 'Pencabutan Gigi',
        //     'description' => 'Prosedur untuk mencabut gigi yang tidak bisa diselamatkan.',
        // ]);

        // $procedureMaterials = [
        //     // Tambal Gigi
        //     ['procedure_id' => 1, 'dental_material_id' => 1, 'quantity' => 2], // Resin Komposit
        //     ['procedure_id' => 1, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi
        //     ['procedure_id' => 1, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
        //     ['procedure_id' => 1, 'dental_material_id' => 4, 'quantity' => 1], // Amalgam (optional)

        //     // Pencabutan Gigi
        //     ['procedure_id' => 3, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi
        //     ['procedure_id' => 3, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
        //     ['procedure_id' => 3, 'dental_material_id' => 1, 'quantity' => 1], // Resin Komposit (pelindung pasca pencabutan)

        //     // Pembersihan Karang Gigi
        //     ['procedure_id' => 2, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
        //     ['procedure_id' => 2, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi (opsional)
        //     ['procedure_id' => 2, 'dental_material_id' => 4, 'quantity' => 1], // Amalgam (kasus tertentu)
        // ];

        // // Loop dan buat satu per satu dengan create()
        // foreach ($procedureMaterials as $data) {
        //     ProcedureMaterial::create($data);
        // }


        // Tambal Gigi - Base Price
        // Pricelist::create([
        //     'procedure_id' => 1,
        //     'price' => 300000, // Harga dasar: 300.000
        //     'is_promo' => false,
        //     'effective_date' => Carbon::now()->subDays(5),
        // ]);

        // // Tambal Gigi - Promo Price
        // Pricelist::create([
        //     'procedure_id' => 1,
        //     'price' => 200000, // Harga promosi: 200.000
        //     'is_promo' => true,
        //     'effective_date' => Carbon::now()->subDays(2),
        // ]);

        // // Pembersihan Karang Gigi - Base Price
        // Pricelist::create([
        //     'procedure_id' => 2,
        //     'price' => 400000, // Harga dasar: 400.000
        //     'is_promo' => false,
        //     'effective_date' => Carbon::now()->subDays(10),
        // ]);

        // // Pembersihan Karang Gigi - Promo Price
        // Pricelist::create([
        //     'procedure_id' => 2,
        //     'price' => 300000, // Harga promosi: 300.000
        //     'is_promo' => true,
        //     'effective_date' => Carbon::now()->subDays(3),
        // ]);

        // // Pencabutan Gigi - Base Price
        // Pricelist::create([
        //     'procedure_id' => 3,
        //     'price' => 500000, // Harga dasar: 500.000
        //     'is_promo' => false,
        //     'effective_date' => Carbon::now()->subDays(7),
        // ]);

        // // Pencabutan Gigi - Promo Price
        // Pricelist::create([
        //     'procedure_id' => 3,
        //     'price' => 350000, // Harga promosi: 350.000
        //     'is_promo' => true,
        //     'effective_date' => Carbon::now()->subDays(1),
        // ]);

        $procedures = [
            ['item_code' => '01A', 'name' => 'Janji Temu', 'procedure_type_id' => 1],
            ['item_code' => '01B', 'name' => 'Konsultasi', 'procedure_type_id' => 1],
            ['item_code' => '01C', 'name' => 'Tambahan Kategori 1', 'procedure_type_id' => 1],
            ['item_code' => '01D', 'name' => 'Tambahan Kategori 2', 'procedure_type_id' => 1],
            ['item_code' => '01E', 'name' => 'Tambahan Kategori 3', 'procedure_type_id' => 1],
            
            ['item_code' => '02A', 'name' => 'Tambal Estetis gigi depan (kecil)', 'procedure_type_id' => 2],
            ['item_code' => '02B', 'name' => 'Tambal Estetis gigi depan (besar)', 'procedure_type_id' => 2],
            ['item_code' => '02C', 'name' => 'Tambal Estetis gigi belakang (class I)', 'procedure_type_id' => 2],
            ['item_code' => '02D', 'name' => 'Tambal Estetis gigi belakang (class II)', 'procedure_type_id' => 2],
            ['item_code' => '02E', 'name' => 'Aplikasi base liner', 'procedure_type_id' => 2],
            ['item_code' => '02F', 'name' => 'Pulp capping', 'procedure_type_id' => 2],
            ['item_code' => '02G', 'name' => 'Fisure sealant', 'procedure_type_id' => 2],
            ['item_code' => '02H', 'name' => 'Tambalan sementara', 'procedure_type_id' => 2],
            ['item_code' => '02I', 'name' => 'Bongkar tambalan', 'procedure_type_id' => 2],
            ['item_code' => '02J', 'name' => 'Occlusal Adjustment', 'procedure_type_id' => 2],
            ['item_code' => '02K', 'name' => 'Inlay/Onlay Porcelain', 'procedure_type_id' => 2],
            
            ['item_code' => '03A', 'name' => 'Devitalisasi', 'procedure_type_id' => 3],
            ['item_code' => '03B', 'name' => 'Medikasi', 'procedure_type_id' => 3],
            ['item_code' => '03C', 'name' => '1 saluran akar (sampai dengan pengisian)', 'procedure_type_id' => 3],
            ['item_code' => '03D', 'name' => '2 saluran akar (sampai dengan pengisian)', 'procedure_type_id' => 3],
            ['item_code' => '03E', 'name' => '3 saluran akar (sampai dengan pengisian)', 'procedure_type_id' => 3],
            ['item_code' => '03F', 'name' => '4 atau lebih saluran akar', 'procedure_type_id' => 3],
            ['item_code' => '03G', 'name' => 'Metal alloy Post and core', 'procedure_type_id' => 3],
            ['item_code' => '03H', 'name' => 'Fiber Post and core', 'procedure_type_id' => 3],
            ['item_code' => '03I', 'name' => 'Bongkar pengisian', 'procedure_type_id' => 3],
            ['item_code' => '03J', 'name' => 'Mumifikasi', 'procedure_type_id' => 3 ],
            
            ['item_code' => '04A', 'name' => 'Bongkar crown','procedure_type_id' => 4],
            ['item_code' => '04B', 'name' => 'Full metal Crown','procedure_type_id' => 4],
            ['item_code' => '04C', 'name' => 'PFM Crown','procedure_type_id' => 4],
            ['item_code' => '04D', 'name' => 'Premium lab composite Crown','procedure_type_id' => 4],
            ['item_code' => '04E', 'name' => 'E-max / Zirconia Crown','procedure_type_id' => 4],
            ['item_code' => '04F', 'name' => 'Provisoris','procedure_type_id' => 4],
            ['item_code' => '04G', 'name' => 'Cetak alginat','procedure_type_id' => 4],
            ['item_code' => '04H', 'name' => 'Cetak PVS','procedure_type_id' => 4],
            ['item_code' => '04I', 'name' => 'Re-sementasi','procedure_type_id' => 4],
            ['item_code' => '04J', 'name' => 'Wax up per gigi','procedure_type_id' => 4],
            
            ['item_code' => '05A', 'name' => 'Cabut gigi depan','procedure_type_id' => 5],
            ['item_code' => '05B', 'name' => 'Cabut gigi belakang','procedure_type_id' => 5],
            ['item_code' => '05C', 'name' => 'Cabut komplikasi depan atau belakang','procedure_type_id' => 5],
            ['item_code' => '05D', 'name' => 'Penjahitan','procedure_type_id' => 5],
            ['item_code' => '05E', 'name' => 'Impaksi simple','procedure_type_id' => 5],
            ['item_code' => '05F', 'name' => 'Impaksi kompleks','procedure_type_id' => 5],
            ['item_code' => '05G', 'name' => 'Kuretase per gigi','procedure_type_id' => 5],
            ['item_code' => '05H', 'name' => 'Spongostan','procedure_type_id' => 5],
            ['item_code' => '05I', 'name' => 'Eksisi','procedure_type_id' => 5],
            ['item_code' => '05J', 'name' => 'Lepas Jahitan','procedure_type_id' => 5],
            
            ['item_code' => '06A', 'name' => 'Cabut non injeksi','procedure_type_id' => 6],
            ['item_code' => '06B', 'name' => 'Cabut dengan injeksi','procedure_type_id' => 6],
            ['item_code' => '06C', 'name' => 'Penambalan dengan GIC','procedure_type_id' => 6],
            ['item_code' => '06D', 'name' => 'Topikal aplikasi fluor 1 mulut','procedure_type_id' => 6],
            ['item_code' => '06E', 'name' => 'Perawatan saraf anak, hingga selesai','procedure_type_id' => 6],
            
            ['item_code' => '07A', 'name' => 'Acrylic (Frame saja)','procedure_type_id' => 7],
            ['item_code' => '07B', 'name' => 'Flexible with Valplast/ Thermosens (Frame saja)','procedure_type_id' => 7],
            ['item_code' => '07C', 'name' => 'Metal Alloy frame kombinasi Acrylic (Frame saja)','procedure_type_id' => 7],
            ['item_code' => '07D', 'name' => 'Rebasing recountour/repair','procedure_type_id' => 7],
            ['item_code' => '07E', 'name' => 'Tambahan per gigi acrylic','procedure_type_id' => 7],
            ['item_code' => '07F', 'name' => 'Gigi tiruan lengkap per rahang komplit','procedure_type_id' => 7],
            ['item_code' => '07G', 'name' => 'Tambahan per gigi valplast','procedure_type_id' => 7],
            ['item_code' => '07H', 'name' => 'Tambahan per gigi metal frame','procedure_type_id' => 7],
            
            ['item_code' => '08A', 'name' => 'Scaling Grade 1','procedure_type_id' => 8],
            ['item_code' => '08B', 'name' => 'Scaling Grade 2','procedure_type_id' => 8],
            ['item_code' => '08C', 'name' => 'Scaling Grade 3','procedure_type_id' => 8],
            ['item_code' => '08D', 'name' => 'Stain removal dan oral profilaksis','procedure_type_id' => 8],
            ['item_code' => '08E', 'name' => 'Root planning/curetage (1 regio)','procedure_type_id' => 8],
            ['item_code' => '08F', 'name' => 'Splinting fiber (3-6 unit)','procedure_type_id' => 8],
            ['item_code' => '08G', 'name' => 'Medikasi periodontal per gigi','procedure_type_id' => 8],
            ['item_code' => '08H', 'name' => 'Medikasi 1 regio','procedure_type_id' => 8],
            
            ['item_code' => '09A', 'name' => 'Whitening in office (2x aplikasi)','procedure_type_id' => 9],
            ['item_code' => '09B', 'name' => 'Whitening in office (4x aplikasi)','procedure_type_id' => 9],
            ['item_code' => '09C', 'name' => 'Home whitening per rahang','procedure_type_id' => 9],
            ['item_code' => '09D', 'name' => 'Diamond','procedure_type_id' => 9],
            ['item_code' => '09E', 'name' => 'Veneer komposit per gigi','procedure_type_id' => 9],
            ['item_code' => '09F', 'name' => 'Veneer porcelain per gigi','procedure_type_id' => 9],
            ['item_code' => '09G', 'name' => 'Sementasi dengan resin semen','procedure_type_id' => 9],
            ['item_code' => '09H', 'name' => 'Internal Bleaching per gigi','procedure_type_id' => 9],
            
            ['item_code' => '10A', 'name' => 'Pemasangan Metal Bracket komplit','procedure_type_id' => 10],
            ['item_code' => '10B', 'name' => 'Metal 1 rahang','procedure_type_id' => 10],
            ['item_code' => '10C', 'name' => 'Pemasangan Metal Bracket premium komplit','procedure_type_id' => 10],
            ['item_code' => '10D', 'name' => 'Ceramic','procedure_type_id' => 10],
            ['item_code' => '10E', 'name' => 'Ceramic 1 rahang','procedure_type_id' => 10],
            ['item_code' => '10F', 'name' => 'Ceramic premium','procedure_type_id' => 10],
            ['item_code' => '10G', 'name' => 'Ceramic premium 1 rahang','procedure_type_id' => 10],
            ['item_code' => '10H', 'name' => 'Self ligating','procedure_type_id' => 10],
            ['item_code' => '10I', 'name' => 'Self ligating 1 rahang','procedure_type_id' => 10],
            ['item_code' => '10J', 'name' => 'Piranti ortho lepasan per rahang','procedure_type_id' => 10],
            ['item_code' => '10K', 'name' => 'Slicing / rahang','procedure_type_id' => 10],
            ['item_code' => '10L', 'name' => 'Kontrol (RA, RB)','procedure_type_id' => 10],
            ['item_code' => '10M', 'name' => 'Ganti Kawat NiTi (per rahang)','procedure_type_id' => 10],
            ['item_code' => '10N', 'name' => 'Ganti Kawat Thermal (per rahang)','procedure_type_id' => 10],
            ['item_code' => '10O', 'name' => 'Reverse wire per rahang','procedure_type_id' => 10],
            ['item_code' => '10P', 'name' => 'Pasang button, crimpable hook, ocs','procedure_type_id' => 10],
            ['item_code' => '10Q', 'name' => 'Peninggian gigit per gigi','procedure_type_id' => 10],
            ['item_code' => '10R', 'name' => 'Pasang molar band per gigi','procedure_type_id' => 10],
            ['item_code' => '10S', 'name' => 'Pasang attachment per gigi','procedure_type_id' => 10],
            ['item_code' => '10T', 'name' => 'Perekatan kembali bracket lepas','procedure_type_id' => 10],
            ['item_code' => '10U', 'name' => 'Penggantian braket hilang (Metal)','procedure_type_id' => 10],
            ['item_code' => '10V', 'name' => 'Penggantian braket hilang (Ceramic)','procedure_type_id' => 10],
            ['item_code' => '10W', 'name' => 'Penggantian braket hilang (Self Ligating)','procedure_type_id' => 10],
            ['item_code' => '10X', 'name' => 'Night Guard','procedure_type_id' => 10],
            ['item_code' => '10Y', 'name' => 'LMA','procedure_type_id' => 10],
            ['item_code' => '10Y 01', 'name' => 'Myobrace','procedure_type_id' => 10],
            ['item_code' => '10Z', 'name' => 'LMA (BR)','procedure_type_id' => 10],
            
            ['item_code' => '11A', 'name' => 'Retainer hawley (per rahang)','procedure_type_id' => 11],
            ['item_code' => '11B', 'name' => 'Retainer invisible (per rahang)','procedure_type_id' => 11],
            ['item_code' => '11C', 'name' => 'Fixed Retainer','procedure_type_id' => 11],
            ['item_code' => '11D', 'name' => 'Lepas Behel dan scaling','procedure_type_id' => 11],
            ['item_code' => '11E', 'name' => 'Orthokit','procedure_type_id' => 11],
            
            ['item_code' => '12A', 'name' => 'Kontrol (RA, RB)','procedure_type_id' => 12],
            ['item_code' => '12B', 'name' => 'Ganti Kawat NiTi (per rahang)','procedure_type_id' => 12],
            ['item_code' => '12C', 'name' => 'Ganti Kawat Thermal (per rahang)','procedure_type_id' => 12],
            ['item_code' => '12D', 'name' => 'Reverse wire per rahang','procedure_type_id' => 12],
            ['item_code' => '12E', 'name' => 'Pasang button, crimpable hook, ocs','procedure_type_id' => 12],
            ['item_code' => '12F', 'name' => 'Peninggian gigit per gigi','procedure_type_id' => 12],
            ['item_code' => '12G', 'name' => 'Pasang molar band per gigi','procedure_type_id' => 12],
            ['item_code' => '12H', 'name' => 'Pasang attachment per gigi','procedure_type_id' => 12],
            ['item_code' => '12I', 'name' => 'Perekatan kembali bracket lepas','procedure_type_id' => 12],
            ['item_code' => '12J', 'name' => 'Penggantian braket hilang (Metal)','procedure_type_id' => 12],
            ['item_code' => '12K', 'name' => 'Penggantian braket hilang (Ceramic)','procedure_type_id' => 12],
            ['item_code' => '12L', 'name' => 'Penggantian braket hilang (Self Ligating)','procedure_type_id' => 12],
            ['item_code' => '12M', 'name' => 'Lepas Behel dan scaling','procedure_type_id' => 12],
            ['item_code' => '12N', 'name' => 'Analisa Kasus (sederhana)','procedure_type_id' => 12],
            ['item_code' => '12O', 'name' => 'Analisa Kasus (kompleks)','procedure_type_id' => 12],
        ];

        foreach ($procedures as $procedure) {
            Procedure::create([
                'item_code' => $procedure['item_code'],
                'name' => $procedure['name'],
                'procedure_type_id' => $procedure['procedure_type_id'],
                'description' => '', // You can add descriptions if needed
            ]);
        }

        // Bahan Baku
        Category::create([
            'name' => 'Bahan Baku',
        ]);

        // Operasional
        Category::create([
            'name' => 'Operasional',
        ]);

        // Gaji Karyawan
        Category::create([
            'name' => 'Gaji Karyawan',
        ]);

        // Peralatan
        Category::create([
            'name' => 'Peralatan',
        ]);

        // Rumah Tangga
        Category::create([
            'name' => 'Rumah Tangga',
        ]);

        DB::table('holidays')->insert([
            ['tanggal' => '2024-01-01', 'keterangan' => 'Tahun Baru'],
            ['tanggal' => '2024-02-10', 'keterangan' => 'Tahun Baru Imlek'],
            ['tanggal' => '2024-03-11', 'keterangan' => 'Nyepi'],
            ['tanggal' => '2024-04-10', 'keterangan' => 'Idul Fitri'],
            ['tanggal' => '2024-12-25', 'keterangan' => 'Natal'],
        ]);

        DB::table('holidays')->insert([
            ['tanggal' => '2025-01-01', 'keterangan' => 'Tahun Baru 2025 Masehi'],
            ['tanggal' => '2025-01-27', 'keterangan' => 'Isra Mikraj Nabi Muhammad SAW'],
            ['tanggal' => '2025-01-29', 'keterangan' => 'Tahun Baru Imlek 2576 Kongzili'],
            ['tanggal' => '2025-03-29', 'keterangan' => 'Hari Suci Nyepi (Tahun Baru Saka 1947)'],
            ['tanggal' => '2025-03-31', 'keterangan' => 'Idulfitri 1446 Hijriah'],
            ['tanggal' => '2025-04-01', 'keterangan' => 'Idulfitri 1446 Hijriah'],
            ['tanggal' => '2025-04-18', 'keterangan' => 'Wafat Yesus Kristus'],
            ['tanggal' => '2025-04-20', 'keterangan' => 'Kebangkitan Yesus Kristus (Paskah)'],
            ['tanggal' => '2025-05-01', 'keterangan' => 'Hari Buruh Internasional'],
            ['tanggal' => '2025-05-12', 'keterangan' => 'Hari Raya Waisak 2569 BE'],
            ['tanggal' => '2025-05-29', 'keterangan' => 'Kenaikan Yesus Kristus'],
            ['tanggal' => '2025-06-01', 'keterangan' => 'Hari Lahir Pancasila'],
            ['tanggal' => '2025-06-06', 'keterangan' => 'Idul Adha 1446 Hijriah'],
            ['tanggal' => '2025-06-27', 'keterangan' => 'Tahun Baru Islam 1447 Hijriah'],
            ['tanggal' => '2025-08-17', 'keterangan' => 'Proklamasi Kemerdekaan RI'],
            ['tanggal' => '2025-09-05', 'keterangan' => 'Maulid Nabi Muhammad SAW'],
            ['tanggal' => '2025-12-25', 'keterangan' => 'Hari Raya Natal'],
        ]);

        // $accounts = [
        //     // Aset Lancar (Current Assets)
        //     ['code' => '1-10001', 'name' => 'Kas', 'type' => 'asset'],
        //     ['code' => '1-10002', 'name' => 'Rekening Bank', 'type' => 'asset'],
        //     ['code' => '1-10003', 'name' => 'SQ01 Bank Mandiri', 'type' => 'asset'],
        //     ['code' => '1-10004', 'name' => 'Pusat Bank BCA', 'type' => 'asset'],
        //     ['code' => '1-10005', 'name' => 'SQ01 Petty Cash', 'type' => 'asset'],
        //     ['code' => '1-10006', 'name' => 'SQ01 Bank CIMB Niaga', 'type' => 'asset'],
        //     ['code' => '1-10007', 'name' => 'SQ01 Permata', 'type' => 'asset'],
        //     ['code' => '1-10008', 'name' => 'SQ01 Bank BCA', 'type' => 'asset'],
        //     ['code' => '1-10010', 'name' => 'Pinjaman Direksi', 'type' => 'asset'],
        //     ['code' => '1-10012', 'name' => 'SQ01 Bank BCA (Cabang XYZ)', 'type' => 'asset'],

        //     // Piutang Usaha
        //     ['code' => '1-10100', 'name' => 'Piutang Usaha', 'type' => 'asset'],

        //     // Persediaan
        //     //ini 12 13
        //     ['code' => '1-10200', 'name' => 'Persediaan Barang', 'type' => 'asset'],
        //     ['code' => '1-10201', 'name' => 'Persediaan Barang Medis', 'type' => 'asset'],

        //     // Kewajiban (Liabilities)
        //     ['code' => '2-10001', 'name' => 'Utang Usaha', 'type' => 'liability'],
        //     ['code' => '2-10002', 'name' => 'Utang Pajak', 'type' => 'liability'],

        //     // Ekuitas (Equity)
        //     ['code' => '3-10001', 'name' => 'Modal Pemilik', 'type' => 'equity'],
        //     ['code' => '3-10002', 'name' => 'Laba Ditahan', 'type' => 'equity'],

        //     // Pendapatan (Revenue)
        //     ['code' => '4-10001', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],

        //     // Beban (Expenses)
        //     ['code' => '5-10001', 'name' => 'Beban Gaji', 'type' => 'expense'],
        //     ['code' => '5-10002', 'name' => 'HPP Bahan Dental', 'type' => 'expense'],
        //     ['code' => '5-10003', 'name' => 'Beban Sewa', 'type' => 'expense'],
        // ];

        // tambahan
        $accounts = [
            // Aset Lancar (Current Assets)
            ['code' => '1-10001', 'name' => 'Kas', 'type' => 'asset'],
            ['code' => '1-10002', 'name' => 'Rekening Bank', 'type' => 'asset'],
            ['code' => '1-10003', 'name' => 'SQ01 Bank Mandiri', 'type' => 'asset'],
            ['code' => '1-10004', 'name' => 'Pusat Bank BCA', 'type' => 'asset'],
            ['code' => '1-10005', 'name' => 'SQ01 Petty Cash', 'type' => 'asset'],
            ['code' => '1-10006', 'name' => 'SQ01 Bank CIMB Niaga', 'type' => 'asset'],
            ['code' => '1-10007', 'name' => 'SQ01 Permata', 'type' => 'asset'],
            ['code' => '1-10008', 'name' => 'SQ01 Bank BCA', 'type' => 'asset'],
            ['code' => '1-10010', 'name' => 'Pinjaman Direksi', 'type' => 'asset'],
            ['code' => '1-10012', 'name' => 'SQ01 Bank BCA (Cabang XYZ)', 'type' => 'asset'],

            // Piutang Usaha (acount receivables)
            ['code' => '1-10100', 'name' => 'Piutang Usaha', 'type' => 'asset'],

            // Persediaan (inventory bahan medis)
            //ini 12 13
            ['code' => '1-10200', 'name' => 'Persediaan Barang', 'type' => 'asset'],
            ['code' => '1-10201', 'name' => 'Persediaan Barang Medis', 'type' => 'asset'],

            // beban dibayar dimuka (prepaid expenses)
            ['code' => '1-10300', 'name' => 'Beban Dibayar di Muka', 'type' => 'asset'],

            // fixed asset dan depreciation : berhubungan kyk tanah dll yang ada dpresiasi nya
            // harusnya ga kepake sih 
            ['code' => '1-10400', 'name' => 'Aset Tetap', 'type' => 'asset'],
            ['code' => '1-10500', 'name' => 'Depresiasi Kumulatif', 'type' => 'asset'],
            ['code' => '1-10600', 'name' => 'Aset Lain-Lain', 'type' => 'asset'],

            // Kewajiban (Liabilities)
            // accounts payable
            ['code' => '2-10001', 'name' => 'Utang Usaha', 'type' => 'liability'],
            ['code' => '2-10002', 'name' => 'Utang Pajak', 'type' => 'liability'],

            // Accrued liabilities harus dicatat pada periode di mana transaksi terjadi, bukan saat pembayaran dilakukan, agar sesuai dengan prinsip akuntansi akrual.
            // Sebagai contoh, jika sebuah perusahaan berlangganan layanan perangkat lunak bulanan senilai Rp5.000.000, tetapi pembayaran dilakukan setiap tiga bulan, maka setiap bulan perusahaan tersebut harus mencatat Rp5.000.000 sebagai accrued liabilities hingga faktur dibayarkan. Setelah pembayaran dilakukan, accrued liabilities akan berkurang.
            // ['code' => '2-20100', 'name' => 'Accrued Liabi', 'type' => 'liability'],

            // Ekuitas (Equity)
            ['code' => '3-10001', 'name' => 'Modal Pemilik', 'type' => 'equity'],
            ['code' => '3-10002', 'name' => 'Laba Ditahan', 'type' => 'equity'],

            // Pendapatan (Revenue)
            ['code' => '4-10001', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],
            // Sales Returns and Allowances adalah suatu peristiwa dimana barang yang kita jual dan telah sampai kepada pelanggan mengalami cacat produksi atau adanya kerusakan ketika terjadi pengiriman yang adanya kesepakatan bahwa risiko pengiriman ditanggung oleh perusahaan.
            // ['code' => '4-10100', 'name' => 'Retur Penjualan dan Pengurangan Harga', 'type' => 'revenue'],

            // Beban (Expenses)
            ['code' => '5-10001', 'name' => 'Beban Gaji', 'type' => 'expense'],
            ['code' => '5-10002', 'name' => 'HPP Bahan Dental', 'type' => 'expense'],
            ['code' => '5-10003', 'name' => 'Beban Sewa', 'type' => 'expense'],
            ['code' => '5-10004', 'name' => 'Beban Iklan', 'type' => 'expense'],
            ['code' => '5-10005', 'name' => 'Biaya Admin Bank', 'type' => 'expense'],
            ['code' => '5-10006', 'name' => 'Beban ATK', 'type' => 'expense'],
            ['code' => '5-10007', 'name' => 'Beban Listrik', 'type' => 'expense'],
            ['code' => '5-10008', 'name' => 'Beban Air', 'type' => 'expense'],
            ['code' => '5-10009', 'name' => 'Beban Internet', 'type' => 'expense'],
            ['code' => '5-10010', 'name' => 'Beban Telepon', 'type' => 'expense'],
            ['code' => '5-10011', 'name' => 'Bagi Hasil Dokter', 'type' => 'expense'],
            ['code' => '5-10012', 'name' => 'Diskon Pembelian', 'type' => 'expense'], // Nilainya negatif, contra expenses
            ['code' => '5-10013', 'name' => 'Beban Pengiriman Pembelian', 'type' => 'expense'],
            // ['code' => '5-10006', 'name' => 'Penyusutan Aset Tetap', 'type' => 'expense'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }

        Supplier::create([
            'nama' => 'supplier1',
            'alamat' => 'kenjeran',
            'nomor_telepon' => '0811100029292',
            'email' => 'supplier1@gmail.com'
        ]);
    }
}
