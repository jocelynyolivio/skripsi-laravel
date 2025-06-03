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
            'role_id' => 3
        ]);
        User::create([
            'name' => 'manager1',
            'email' => 'manager1@gmail.com',
            'password' => bcrypt('manager'),
            'role_id' => 4
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
            ['item_code' => '03J', 'name' => 'Mumifikasi', 'procedure_type_id' => 3],

            ['item_code' => '04A', 'name' => 'Bongkar crown', 'procedure_type_id' => 4],
            ['item_code' => '04B', 'name' => 'Full metal Crown', 'procedure_type_id' => 4],
            ['item_code' => '04C', 'name' => 'PFM Crown', 'procedure_type_id' => 4],
            ['item_code' => '04D', 'name' => 'Premium lab composite Crown', 'procedure_type_id' => 4],
            ['item_code' => '04E', 'name' => 'E-max / Zirconia Crown', 'procedure_type_id' => 4],
            ['item_code' => '04F', 'name' => 'Provisoris', 'procedure_type_id' => 4],
            ['item_code' => '04G', 'name' => 'Cetak alginat', 'procedure_type_id' => 4],
            ['item_code' => '04H', 'name' => 'Cetak PVS', 'procedure_type_id' => 4],
            ['item_code' => '04I', 'name' => 'Re-sementasi', 'procedure_type_id' => 4],
            ['item_code' => '04J', 'name' => 'Wax up per gigi', 'procedure_type_id' => 4],

            ['item_code' => '05A', 'name' => 'Cabut gigi depan', 'procedure_type_id' => 5],
            ['item_code' => '05B', 'name' => 'Cabut gigi belakang', 'procedure_type_id' => 5],
            ['item_code' => '05C', 'name' => 'Cabut komplikasi depan atau belakang', 'procedure_type_id' => 5],
            ['item_code' => '05D', 'name' => 'Penjahitan', 'procedure_type_id' => 5],
            ['item_code' => '05E', 'name' => 'Impaksi simple', 'procedure_type_id' => 5],
            ['item_code' => '05F', 'name' => 'Impaksi kompleks', 'procedure_type_id' => 5],
            ['item_code' => '05G', 'name' => 'Kuretase per gigi', 'procedure_type_id' => 5],
            ['item_code' => '05H', 'name' => 'Spongostan', 'procedure_type_id' => 5],
            ['item_code' => '05I', 'name' => 'Eksisi', 'procedure_type_id' => 5],
            ['item_code' => '05J', 'name' => 'Lepas Jahitan', 'procedure_type_id' => 5],

            ['item_code' => '06A', 'name' => 'Cabut non injeksi', 'procedure_type_id' => 6],
            ['item_code' => '06B', 'name' => 'Cabut dengan injeksi', 'procedure_type_id' => 6],
            ['item_code' => '06C', 'name' => 'Penambalan dengan GIC', 'procedure_type_id' => 6],
            ['item_code' => '06D', 'name' => 'Topikal aplikasi fluor 1 mulut', 'procedure_type_id' => 6],
            ['item_code' => '06E', 'name' => 'Perawatan saraf anak, hingga selesai', 'procedure_type_id' => 6],

            ['item_code' => '07A', 'name' => 'Acrylic (Frame saja)', 'procedure_type_id' => 7],
            ['item_code' => '07B', 'name' => 'Flexible with Valplast/ Thermosens (Frame saja)', 'procedure_type_id' => 7],
            ['item_code' => '07C', 'name' => 'Metal Alloy frame kombinasi Acrylic (Frame saja)', 'procedure_type_id' => 7],
            ['item_code' => '07D', 'name' => 'Rebasing recountour/repair', 'procedure_type_id' => 7],
            ['item_code' => '07E', 'name' => 'Tambahan per gigi acrylic', 'procedure_type_id' => 7],
            ['item_code' => '07F', 'name' => 'Gigi tiruan lengkap per rahang komplit', 'procedure_type_id' => 7],
            ['item_code' => '07G', 'name' => 'Tambahan per gigi valplast', 'procedure_type_id' => 7],
            ['item_code' => '07H', 'name' => 'Tambahan per gigi metal frame', 'procedure_type_id' => 7],

            ['item_code' => '08A', 'name' => 'Scaling Grade 1', 'procedure_type_id' => 8],
            ['item_code' => '08B', 'name' => 'Scaling Grade 2', 'procedure_type_id' => 8],
            ['item_code' => '08C', 'name' => 'Scaling Grade 3', 'procedure_type_id' => 8],
            ['item_code' => '08D', 'name' => 'Stain removal dan oral profilaksis', 'procedure_type_id' => 8],
            ['item_code' => '08E', 'name' => 'Root planning/curetage (1 regio)', 'procedure_type_id' => 8],
            ['item_code' => '08F', 'name' => 'Splinting fiber (3-6 unit)', 'procedure_type_id' => 8],
            ['item_code' => '08G', 'name' => 'Medikasi periodontal per gigi', 'procedure_type_id' => 8],
            ['item_code' => '08H', 'name' => 'Medikasi 1 regio', 'procedure_type_id' => 8],

            ['item_code' => '09A', 'name' => 'Whitening in office (2x aplikasi)', 'procedure_type_id' => 9],
            ['item_code' => '09B', 'name' => 'Whitening in office (4x aplikasi)', 'procedure_type_id' => 9],
            ['item_code' => '09C', 'name' => 'Home whitening per rahang', 'procedure_type_id' => 9],
            ['item_code' => '09D', 'name' => 'Diamond', 'procedure_type_id' => 9],
            ['item_code' => '09E', 'name' => 'Veneer komposit per gigi', 'procedure_type_id' => 9],
            ['item_code' => '09F', 'name' => 'Veneer porcelain per gigi', 'procedure_type_id' => 9],
            ['item_code' => '09G', 'name' => 'Sementasi dengan resin semen', 'procedure_type_id' => 9],
            ['item_code' => '09H', 'name' => 'Internal Bleaching per gigi', 'procedure_type_id' => 9],

            ['item_code' => '10A', 'name' => 'Pemasangan Metal Bracket komplit', 'procedure_type_id' => 10],
            ['item_code' => '10B', 'name' => 'Metal 1 rahang', 'procedure_type_id' => 10],
            ['item_code' => '10C', 'name' => 'Pemasangan Metal Bracket premium komplit', 'procedure_type_id' => 10],
            ['item_code' => '10D', 'name' => 'Ceramic', 'procedure_type_id' => 10],
            ['item_code' => '10E', 'name' => 'Ceramic 1 rahang', 'procedure_type_id' => 10],
            ['item_code' => '10F', 'name' => 'Ceramic premium', 'procedure_type_id' => 10],
            ['item_code' => '10G', 'name' => 'Ceramic premium 1 rahang', 'procedure_type_id' => 10],
            ['item_code' => '10H', 'name' => 'Self ligating', 'procedure_type_id' => 10],
            ['item_code' => '10I', 'name' => 'Self ligating 1 rahang', 'procedure_type_id' => 10],
            ['item_code' => '10J', 'name' => 'Piranti ortho lepasan per rahang', 'procedure_type_id' => 10],
            ['item_code' => '10K', 'name' => 'Slicing / rahang', 'procedure_type_id' => 10],
            ['item_code' => '10L', 'name' => 'Kontrol (RA, RB)', 'procedure_type_id' => 10],
            ['item_code' => '10M', 'name' => 'Ganti Kawat NiTi (per rahang)', 'procedure_type_id' => 10],
            ['item_code' => '10N', 'name' => 'Ganti Kawat Thermal (per rahang)', 'procedure_type_id' => 10],
            ['item_code' => '10O', 'name' => 'Reverse wire per rahang', 'procedure_type_id' => 10],
            ['item_code' => '10P', 'name' => 'Pasang button, crimpable hook, ocs', 'procedure_type_id' => 10],
            ['item_code' => '10Q', 'name' => 'Peninggian gigit per gigi', 'procedure_type_id' => 10],
            ['item_code' => '10R', 'name' => 'Pasang molar band per gigi', 'procedure_type_id' => 10],
            ['item_code' => '10S', 'name' => 'Pasang attachment per gigi', 'procedure_type_id' => 10],
            ['item_code' => '10T', 'name' => 'Perekatan kembali bracket lepas', 'procedure_type_id' => 10],
            ['item_code' => '10U', 'name' => 'Penggantian braket hilang (Metal)', 'procedure_type_id' => 10],
            ['item_code' => '10V', 'name' => 'Penggantian braket hilang (Ceramic)', 'procedure_type_id' => 10],
            ['item_code' => '10W', 'name' => 'Penggantian braket hilang (Self Ligating)', 'procedure_type_id' => 10],
            ['item_code' => '10X', 'name' => 'Night Guard', 'procedure_type_id' => 10],
            ['item_code' => '10Y', 'name' => 'LMA', 'procedure_type_id' => 10],
            ['item_code' => '10Y 01', 'name' => 'Myobrace', 'procedure_type_id' => 10],
            ['item_code' => '10Z', 'name' => 'LMA (BR)', 'procedure_type_id' => 10],

            ['item_code' => '11A', 'name' => 'Retainer hawley (per rahang)', 'procedure_type_id' => 11],
            ['item_code' => '11B', 'name' => 'Retainer invisible (per rahang)', 'procedure_type_id' => 11],
            ['item_code' => '11C', 'name' => 'Fixed Retainer', 'procedure_type_id' => 11],
            ['item_code' => '11D', 'name' => 'Lepas Behel dan scaling', 'procedure_type_id' => 11],
            ['item_code' => '11E', 'name' => 'Orthokit', 'procedure_type_id' => 11],

            ['item_code' => '12A', 'name' => 'Kontrol (RA, RB)', 'procedure_type_id' => 12],
            ['item_code' => '12B', 'name' => 'Ganti Kawat NiTi (per rahang)', 'procedure_type_id' => 12],
            ['item_code' => '12C', 'name' => 'Ganti Kawat Thermal (per rahang)', 'procedure_type_id' => 12],
            ['item_code' => '12D', 'name' => 'Reverse wire per rahang', 'procedure_type_id' => 12],
            ['item_code' => '12E', 'name' => 'Pasang button, crimpable hook, ocs', 'procedure_type_id' => 12],
            ['item_code' => '12F', 'name' => 'Peninggian gigit per gigi', 'procedure_type_id' => 12],
            ['item_code' => '12G', 'name' => 'Pasang molar band per gigi', 'procedure_type_id' => 12],
            ['item_code' => '12H', 'name' => 'Pasang attachment per gigi', 'procedure_type_id' => 12],
            ['item_code' => '12I', 'name' => 'Perekatan kembali bracket lepas', 'procedure_type_id' => 12],
            ['item_code' => '12J', 'name' => 'Penggantian braket hilang (Metal)', 'procedure_type_id' => 12],
            ['item_code' => '12K', 'name' => 'Penggantian braket hilang (Ceramic)', 'procedure_type_id' => 12],
            ['item_code' => '12L', 'name' => 'Penggantian braket hilang (Self Ligating)', 'procedure_type_id' => 12],
            ['item_code' => '12M', 'name' => 'Lepas Behel dan scaling', 'procedure_type_id' => 12],
            ['item_code' => '12N', 'name' => 'Analisa Kasus (sederhana)', 'procedure_type_id' => 12],
            ['item_code' => '12O', 'name' => 'Analisa Kasus (kompleks)', 'procedure_type_id' => 12],
        ];

        foreach ($procedures as $procedure) {
            Procedure::create([
                'item_code' => $procedure['item_code'],
                'name' => $procedure['name'],
                'procedure_type_id' => $procedure['procedure_type_id'],
                'description' => '', // You can add descriptions if needed
            ]);
        }

        $prices = [
            20000,
            50000,
            50000,
            150000,
            250000,
            300000,
            350000,
            300000,
            375000,
            100000,
            125000,
            200000,
            100000,
            35000,
            100000,
            1200000,
            200000,
            150000,
            800000,
            1000000,
            1250000,
            1700000,
            300000,
            350000,
            200000,
            200000,
            100000,
            750000,
            1350000,
            750000,
            3000000,
            200000,
            80000,
            200000,
            100000,
            75000,
            250000,
            350000,
            500000,
            100000,
            2500000,
            4000000,
            150000,
            150000,
            150000,
            100000,
            100000,
            175000,
            150000,
            300000,
            400000,
            700000,
            1300000,
            2100000,
            300000,
            100000,
            4000000,
            150000,
            250000,
            250000,
            300000,
            350000,
            50000,
            500000,
            1750000,
            125000,
            450000,
            800000,
            1500000,
            800000,
            250000,
            500000,
            2750000,
            400000,
            400000,
            3500000,
            2500000,
            5000000,
            5000000,
            4000000,
            7000000,
            5500000,
            8500000,
            7500000,
            1300000,
            100000,
            100000,
            50000,
            100000,
            100000,
            50000,
            50000,
            100000,
            25000,
            50000,
            100000,
            275000,
            900000,
            550000,
            3500000,
            2500000,
            3750000,
            350000,
            450000,
            500000,
            500000,
            50000,
            200000,
            100000,
            200000,
            200000,
            100000,
            100000,
            200000,
            50000,
            100000,
            200000,
            550000,
            1800000,
            750000,
            600000,
            1200000
        ];

        $now = Carbon::now();
        $effectiveDate = Carbon::create(2025, 12, 31);

        foreach ($prices as $index => $price) {
            Pricelist::create([
                'procedure_id'    => $index + 1,
                'price'           => $price,
                'is_promo'        => false,
                'effective_date'  => $effectiveDate,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
        }

        $materials = [
            // Umum & Konsul
            ['name' => 'POLIBIB', 'description' => 'Disposable patient bib', 'unit_type' => 'pcs'],
            ['name' => 'SALIVA EJECTOR', 'description' => 'Disposable saliva ejector tip', 'unit_type' => 'pcs'],
            ['name' => 'GELAS KUMUR', 'description' => 'Disposable cup', 'unit_type' => 'pcs'],
            ['name' => 'HANDSCOON XS', 'description' => 'Disposable gloves, size XS', 'unit_type' => 'pcs'],
            ['name' => 'HANDSCOON M', 'description' => 'Disposable gloves, size M', 'unit_type' => 'pcs'],
            ['name' => 'NURSE CAP', 'description' => 'Disposable nurse cap', 'unit_type' => 'pcs'],
            ['name' => 'ALKOHOL 70%', 'description' => 'Alcohol 70% solution', 'unit_type' => 'ml'],
            ['name' => 'MASKER KARET PUTIH', 'description' => 'Disposable face mask, white, earloop', 'unit_type' => 'pcs'],

            // Tambal
            ['name' => 'PREPARASI BUR', 'description' => 'Dental bur for preparation', 'unit_type' => 'pcs'],
            ['name' => 'ETCHING', 'description' => 'Dental etching gel/liquid', 'unit_type' => 'ml'], // Koreksi dari ECTHING
            ['name' => 'COTTON ROLL', 'description' => 'Dental cotton roll', 'unit_type' => 'pcs'], // Koreksi dari CATTON ROLL
            ['name' => 'BONDING', 'description' => 'Dental bonding agent', 'unit_type' => 'ml'],
            ['name' => 'MICROBRUSH', 'description' => 'Disposable micro applicator brush', 'unit_type' => 'pcs'],
            // TODO: User, verifikasi apakah LIGHTCURE adalah material habis pakai (misal shield/tip) atau alat. Jika alat, hapus dari sini.
            ['name' => 'LIGHTCURE', 'description' => 'Item related to light curing (e.g. disposable tip/shield)', 'unit_type' => 'pcs'],
            ['name' => 'COMPOSIT', 'description' => 'Dental composite resin', 'unit_type' => 'gr'],
            ['name' => 'FLOW', 'description' => 'Flowable dental composite resin', 'unit_type' => 'gr'],
            ['name' => 'ARTICULATING PAPER', 'description' => 'Articulating paper sheet/strip', 'unit_type' => 'pcs'], // Misal per lembar
            ['name' => 'GIC', 'description' => 'Glass Ionomer Cement (application/capsule)', 'unit_type' => 'unit'], // 'unit' atau 'aplikasi'
            ['name' => 'ANTISEPTIC GEL', 'description' => 'Antiseptic gel for dental use', 'unit_type' => 'ml'],

            // Scaling
            ['name' => 'PASTA POLES', 'description' => 'Polishing paste', 'unit_type' => 'gr'],

            // Cabut
            ['name' => 'PEHACAIN', 'description' => 'Local anesthetic solution (Pehacain)', 'unit_type' => 'ampul'],
            ['name' => 'ANASTESI TOPICAL', 'description' => 'Topical anesthetic gel/cream', 'unit_type' => 'gr'],
            ['name' => 'KASSA TAMPON', 'description' => 'Gauze tampon/pad', 'unit_type' => 'pcs'],
            ['name' => 'BETADINE', 'description' => 'Betadine antiseptic solution', 'unit_type' => 'ml'],
            ['name' => 'SPONGESTAN', 'description' => 'Hemostatic absorbable gelatin sponge', 'unit_type' => 'pcs'],

            // PSA
            ['name' => 'CAIRAN AQUADES', 'description' => 'Distilled water (Aqua Dest)', 'unit_type' => 'ml'], // cc dikonversi ke ml
            ['name' => 'CAIRAN CAOH', 'description' => 'Calcium Hydroxide solution', 'unit_type' => 'ml'], // cc dikonversi ke ml
            // TODO: User, verifikasi CAVITRON. Jika ini alat, mungkin yang dimaksud adalah tip atau powdernya.
            ['name' => 'CAVITRON', 'description' => 'Consumable for Cavitron scaler (e.g. powder/tip)', 'unit_type' => 'gr'],
            ['name' => 'COTTON PELLET', 'description' => 'Dental cotton pellet', 'unit_type' => 'pcs'], // Koreksi dari CATTON PELET
            ['name' => 'PAPER POINT 15-40', 'description' => 'Assorted paper points (ISO 15-40)', 'unit_type' => 'pcs'], // Asumsi 1 pack/set jika beragam ukuran
            ['name' => 'PAPER POINT F1', 'description' => 'Paper point, size F1', 'unit_type' => 'pcs'],
            ['name' => 'PAPER POINT F2', 'description' => 'Paper point, size F2', 'unit_type' => 'pcs'],
            ['name' => 'PAPER POINT F3', 'description' => 'Paper point, size F3', 'unit_type' => 'pcs'],
            ['name' => 'GUTTAPERCHA 15-40', 'description' => 'Assorted Gutta Percha points (ISO 15-40)', 'unit_type' => 'pcs'], // Koreksi dari GUTTAP, asumsi 1 pack/set
            ['name' => 'GUTTAPERCHA F1', 'description' => 'Gutta Percha point, size F1', 'unit_type' => 'pcs'],
            ['name' => 'GUTTAPERCHA F2', 'description' => 'Gutta Percha point, size F2', 'unit_type' => 'pcs'],
            ['name' => 'GUTTAPERCHA F3', 'description' => 'Gutta Percha point, size F3', 'unit_type' => 'pcs'],
            ['name' => 'SPIRITUS', 'description' => 'Spiritus/Medical alcohol', 'unit_type' => 'ml'],
            ['name' => 'ANY SEAL', 'description' => 'Root canal sealer (e.g., Any Seal brand)', 'unit_type' => 'gr'], // Asumsi unit gr, bisa juga ml
            ['name' => 'CAPLUS', 'description' => 'Material, possibly for capping or filling (e.g., Caplus brand)', 'unit_type' => 'gr'], // Asumsi unit gr
        ];

        foreach ($materials as $material) {
            DentalMaterial::firstOrCreate(
                ['name' => $material['name']],
                [
                    'description' => $material['description'],
                    'unit_type' => $material['unit_type']
                ]
            );
        }

        $procedureMappings = [
            'KONSUL' => [
                'item_code' => '01B', // Konsultasi
                'materials' => [
                    ['name' => 'POLIBIB', 'quantity' => 1],
                    ['name' => 'SALIVA EJECTOR', 'quantity' => 1],
                    ['name' => 'GELAS KUMUR', 'quantity' => 1],
                    ['name' => 'HANDSCOON XS', 'quantity' => 4],
                    ['name' => 'HANDSCOON M', 'quantity' => 2],
                    ['name' => 'NURSE CAP', 'quantity' => 1],
                    ['name' => 'ALKOHOL 70%', 'quantity' => 20],
                    ['name' => 'MASKER KARET PUTIH', 'quantity' => 1],
                ]
            ],
            'TAMBAL' => [
                'item_code' => '02A', // Tambal Estetis gigi depan (kecil)
                'materials' => [
                    ['name' => 'POLIBIB', 'quantity' => 1],
                    ['name' => 'SALIVA EJECTOR', 'quantity' => 1],
                    ['name' => 'GELAS KUMUR', 'quantity' => 1],
                    ['name' => 'HANDSCOON XS', 'quantity' => 4],
                    ['name' => 'HANDSCOON M', 'quantity' => 2],
                    ['name' => 'NURSE CAP', 'quantity' => 1],
                    ['name' => 'PREPARASI BUR', 'quantity' => 2],
                    ['name' => 'ETCHING', 'quantity' => 0.1],
                    ['name' => 'COTTON ROLL', 'quantity' => 4],
                    ['name' => 'BONDING', 'quantity' => 0.05],
                    ['name' => 'MICROBRUSH', 'quantity' => 1],
                    ['name' => 'LIGHTCURE', 'quantity' => 1],
                    ['name' => 'COMPOSIT', 'quantity' => 0.175],
                    ['name' => 'FLOW', 'quantity' => 0.1],
                    ['name' => 'ARTICULATING PAPER', 'quantity' => 0.5], // 1/2 lembar
                    ['name' => 'GIC', 'quantity' => 1],
                    ['name' => 'ALKOHOL 70%', 'quantity' => 20],
                    ['name' => 'ANTISEPTIC GEL', 'quantity' => 10],
                    ['name' => 'MASKER KARET PUTIH', 'quantity' => 1],
                ]
            ],
            'SCALING' => [
                'item_code' => '08A', // Scaling Grade 1
                'materials' => [
                    ['name' => 'POLIBIB', 'quantity' => 1],
                    ['name' => 'SALIVA EJECTOR', 'quantity' => 1],
                    ['name' => 'GELAS KUMUR', 'quantity' => 1],
                    ['name' => 'HANDSCOON XS', 'quantity' => 4],
                    ['name' => 'HANDSCOON M', 'quantity' => 2],
                    ['name' => 'NURSE CAP', 'quantity' => 1],
                    ['name' => 'PASTA POLES', 'quantity' => 1.6],
                    ['name' => 'ALKOHOL 70%', 'quantity' => 20],
                    ['name' => 'ANTISEPTIC GEL', 'quantity' => 10], // Dari gambar, kolom scaling ada Antiseptic Gel
                    ['name' => 'MASKER KARET PUTIH', 'quantity' => 1],
                ]
            ],
            'CABUT' => [
                'item_code' => '05A', // Cabut gigi depan
                'materials' => [
                    ['name' => 'POLIBIB', 'quantity' => 1],
                    ['name' => 'SALIVA EJECTOR', 'quantity' => 1],
                    ['name' => 'GELAS KUMUR', 'quantity' => 1],
                    ['name' => 'HANDSCOON XS', 'quantity' => 4],
                    ['name' => 'HANDSCOON M', 'quantity' => 2],
                    ['name' => 'NURSE CAP', 'quantity' => 1],
                    ['name' => 'PEHACAIN', 'quantity' => 1],
                    ['name' => 'ANASTESI TOPICAL', 'quantity' => 1],
                    ['name' => 'KASSA TAMPON', 'quantity' => 7],
                    ['name' => 'BETADINE', 'quantity' => 1],
                    ['name' => 'ALKOHOL 70%', 'quantity' => 20],
                    ['name' => 'ANTISEPTIC GEL', 'quantity' => 10],
                    ['name' => 'MASKER KARET PUTIH', 'quantity' => 1],
                    ['name' => 'SPONGESTAN', 'quantity' => 1],
                ]
            ],
            'PSA' => [
                'item_code' => '03C', // 1 saluran akar (sampai dengan pengisian)
                'materials' => [
                    ['name' => 'POLIBIB', 'quantity' => 1],
                    ['name' => 'SALIVA EJECTOR', 'quantity' => 1],
                    ['name' => 'GELAS KUMUR', 'quantity' => 1],
                    ['name' => 'HANDSCOON XS', 'quantity' => 4],
                    ['name' => 'HANDSCOON M', 'quantity' => 2],
                    ['name' => 'NURSE CAP', 'quantity' => 1],
                    ['name' => 'CAIRAN AQUADES', 'quantity' => 3],
                    ['name' => 'CAIRAN CAOH', 'quantity' => 3],
                    ['name' => 'COTTON ROLL', 'quantity' => 7],
                    ['name' => 'CAVITRON', 'quantity' => 2], // Verifikasi ini
                    ['name' => 'COTTON PELLET', 'quantity' => 5],
                    ['name' => 'PAPER POINT 15-40', 'quantity' => 1], // Asumsi 1 pack/set
                    ['name' => 'PAPER POINT F1', 'quantity' => 6], // Asumsi 4-6 menjadi 6
                    ['name' => 'PAPER POINT F2', 'quantity' => 6],
                    ['name' => 'PAPER POINT F3', 'quantity' => 6],
                    ['name' => 'GUTTAPERCHA 15-40', 'quantity' => 1], // Asumsi 1 pack/set
                    ['name' => 'GUTTAPERCHA F1', 'quantity' => 3], // Asumsi 1-3 menjadi 3
                    ['name' => 'GUTTAPERCHA F2', 'quantity' => 3],
                    ['name' => 'GUTTAPERCHA F3', 'quantity' => 3],
                    ['name' => 'SPIRITUS', 'quantity' => 5],
                    ['name' => 'ANY SEAL', 'quantity' => 0.05],
                    ['name' => 'CAPLUS', 'quantity' => 0.50],
                    ['name' => 'ALKOHOL 70%', 'quantity' => 20],
                    ['name' => 'ANTISEPTIC GEL', 'quantity' => 10],
                    ['name' => 'MASKER KARET PUTIH', 'quantity' => 1],
                ]
            ],
        ];

        foreach ($procedureMappings as $key => $procData) {
            $procedure = Procedure::where('item_code', $procData['item_code'])->first();

            if ($procedure) {
                foreach ($procData['materials'] as $matData) {
                    $dentalMaterial = DentalMaterial::where('name', $matData['name'])->first();
                    if ($dentalMaterial) {
                        ProcedureMaterial::firstOrCreate(
                            [
                                'procedure_id' => $procedure->id,
                                'dental_material_id' => $dentalMaterial->id,
                            ],
                            [
                                'quantity' => $matData['quantity']
                            ]
                        );
                    } else {
                        $this->command->warn("Material not found: " . $matData['name'] . ". Skipping for procedure " . $procedure->name);
                    }
                }
            } else {
                $this->command->warn("Procedure not found with item_code: " . $procData['item_code'] . ". Skipping linkage for " . $key);
            }
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

        $accounts = [
            // Aset Lancar (Current Assets)
            ['code' => '1-10001', 'name' => 'Kas', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10002', 'name' => 'Rekening Bank', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'], // General bank account
            ['code' => '1-10003', 'name' => 'SQ01 Bank Mandiri', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10004', 'name' => 'Pusat Bank BCA', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10005', 'name' => 'SQ01 Petty Cash', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10006', 'name' => 'SQ01 Bank CIMB Niaga', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10007', 'name' => 'SQ01 Permata', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10008', 'name' => 'SQ01 Bank BCA', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],
            ['code' => '1-10012', 'name' => 'SQ01 Bank BCA (Cabang XYZ)', 'type' => 'asset', 'is_cash_equivalent' => true, 'cash_flow_activity' => 'none'],

            // Pinjaman Direksi: Ini bisa rumit. Jika direksi memberi pinjaman ke perusahaan, itu arus kas masuk pendanaan. Jika perusahaan memberi pinjaman ke direksi, itu arus kas keluar investasi (jika dianggap investasi) atau operasional (jika terkait operasional). Asumsi ini adalah PIUTANG kepada direksi.
            ['code' => '1-10010', 'name' => 'Pinjaman Direksi', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'investing'], // Atau 'financing' jika perusahaan yg terima pinjaman. Dari sisi aset, ini adalah piutang ke direksi.

            // Piutang Usaha (Accounts Receivables)
            ['code' => '1-10100', 'name' => 'Piutang Usaha', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],

            // Persediaan (Inventory)
            ['code' => '1-10200', 'name' => 'Persediaan Barang', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '1-10201', 'name' => 'Persediaan Barang Medis', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],

            // Beban Dibayar di Muka (Prepaid Expenses)
            ['code' => '1-10300', 'name' => 'Beban Dibayar di Muka', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],

            // Aset Tetap dan Akumulasi Penyusutan
            ['code' => '1-10400', 'name' => 'Aset Tetap', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'investing'], // Pembelian/penjualan aset tetap
            ['code' => '1-10500', 'name' => 'Depresiasi Kumulatif', 'type' => 'contra_asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'none'], // Non-kas, penyesuaian di metode tidak langsung
            ['code' => '1-10600', 'name' => 'Aset Lain-Lain', 'type' => 'asset', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'investing'], // Tergantung sifatnya, bisa investasi

            // Kewajiban (Liabilities)
            ['code' => '2-10001', 'name' => 'Utang Usaha', 'type' => 'liability', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '2-10002', 'name' => 'Utang Pajak', 'type' => 'liability', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Pembayaran pajak adalah operasional

            // Ekuitas (Equity)
            ['code' => '3-10001', 'name' => 'Modal Pemilik', 'type' => 'equity', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'financing'], // Setoran/penarikan modal
            ['code' => '3-10002', 'name' => 'Laba Ditahan', 'type' => 'equity', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'none'], // Akumulasi laba, perubahan non-kas kecuali ada dividen

            // Pendapatan (Revenue)
            ['code' => '4-10001', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '4-10002', 'name' => 'Pendapatan Penyesuaian Persediaan', 'type' => 'revenue', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Biasanya non-kas atau terkait HPP
            ['code' => '4-10101', 'name' => 'Diskon Penjualan', 'type' => 'contra_revenue', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Mengurangi penerimaan kas operasional

            // Beban (Expenses)
            ['code' => '5-10001', 'name' => 'Beban Gaji', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10002', 'name' => 'HPP Bahan Dental', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Terkait persediaan & utang usaha
            ['code' => '5-10003', 'name' => 'Beban Sewa', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10004', 'name' => 'Beban Iklan', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10005', 'name' => 'Biaya Admin Bank', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10006', 'name' => 'Beban ATK', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10007', 'name' => 'Beban Listrik', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10008', 'name' => 'Beban Air', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10009', 'name' => 'Beban Internet', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10010', 'name' => 'Beban Telepon', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10011', 'name' => 'Bagi Hasil Dokter', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10012', 'name' => 'Diskon Pembelian', 'type' => 'contra_expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Mengurangi pengeluaran kas operasional
            ['code' => '5-10013', 'name' => 'Beban Pengiriman Pembelian', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'],
            ['code' => '5-10014', 'name' => 'Beban Penyesuaian Persediaan', 'type' => 'expense', 'is_cash_equivalent' => false, 'cash_flow_activity' => 'operating'], // Biasanya non-kas
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

        $suppliers = [
            // Contoh dari Anda (jika ingin disertakan dengan data spesifik)
            // [
            //     'nama' => 'supplier1',
            //     'alamat' => 'kenjeran',
            //     'nomor_telepon' => '0811100029292',
            //     'email' => 'supplier1@gmail.com'
            // ],

            // Data dari gambar
            ['nama' => 'Tokopedia - Alice Dental', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'Tokopedia - Andini Dental Supply', 'alamat' => null],
            ['nama' => 'Tokopedia - DENPRO Dental Store', 'alamat' => 'Kota Malang'],
            ['nama' => 'Tokopedia - Cobra Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - Dental Grosir', 'alamat' => 'Kab. Sidoarjo'],
            ['nama' => 'Toko Utama Grosir', 'alamat' => 'Kota Malang'],
            ['nama' => 'Tokopedia - Dental Supply', 'alamat' => 'Jl. Dokter Cipto No. 22, Malang'],
            ['nama' => 'Cahaya Alam Sutat', 'alamat' => 'Jl. Embong Wungu'],
            ['nama' => 'Toko Sumber Rejeki', 'alamat' => null],
            ['nama' => 'Tokopedia - TKShops', 'alamat' => 'Kota Surabaya'],
            ['nama' => 'Toko Bahan Gigi Murah Care (THC)', 'alamat' => 'Surabaya'],
            ['nama' => 'SQH Sewa Ruko', 'alamat' => null],
            ['nama' => 'Dentalogi', 'alamat' => null],
            ['nama' => 'Toko Andayana', 'alamat' => null],
            ['nama' => 'SQH Dental Store', 'alamat' => null],
            ['nama' => 'Tokopedia - Vention Official Store', 'alamat' => null],
            ['nama' => 'Tokopedia - Sentra Dental Grosir', 'alamat' => null],
            ['nama' => 'Toko Sarjana Sawangan', 'alamat' => null],
            ['nama' => 'Tokopedia - Eos Dental (Eos)', 'alamat' => null],
            ['nama' => 'SQH POIJ', 'alamat' => null],
            ['nama' => 'Mitra Dental Malang (laboratorium Retainer)', 'alamat' => null],
            ['nama' => 'SQH PDAM', 'alamat' => null],
            ['nama' => 'Toko Sinar Jaya', 'alamat' => 'Jl. Brigjen Slamet Riadi No. 4A, Oro-Oro Dowo - Malang'],
            ['nama' => 'Tokopedia - Dental Jaya - Yogyakarta', 'alamat' => 'Online - Tokopedia'], // Link di gambar
            ['nama' => 'Tokopedia - My Dentist Store', 'alamat' => 'Online - Tokopedia'], // Link di gambar
            ['nama' => 'Tokopedia - CNR Dental', 'alamat' => 'Jakarta Selatan'],
            ['nama' => 'Karya Mandiri Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - Laris Blessing Jakarta Barat', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'Cahaya Terang 2', 'alamat' => 'Jl. Jaksa Agung Suprapto No. 40/A.3'],
            ['nama' => 'Toko Sumber Jaya', 'alamat' => null],
            ['nama' => 'Surya Dental Lab', 'alamat' => null],
            ['nama' => 'Tokopedia Dental', 'alamat' => null],
            ['nama' => 'Depo Bangunan PT Magadaegro Indonesia', 'alamat' => 'Jl. Raya Kemplo No. 69 Singosari, Malang'],
            ['nama' => 'Tokopedia - Medilab Dental', 'alamat' => null],
            ['nama' => 'PT. Camic Indonesia', 'alamat' => null],
            // 'Cobra Dental' sudah ada di atas sebagai 'Tokopedia - Cobra Dental', bisa jadi duplikat atau entitas berbeda. Saya skip untuk menghindari duplikasi nama jika merujuk hal yang sama.
            ['nama' => 'Abadi Dental Lab', 'alamat' => 'SURABAYA'],
            ['nama' => 'Tokopedia - Alkes ID', 'alamat' => 'BEKASI'],
            ['nama' => 'Tokopedia - Healthy Dent', 'alamat' => 'JAKARTA TIMUR'],
            ['nama' => 'FASSAN - DENT', 'alamat' => 'MALANG'],
            ['nama' => 'PT. Cobra Dental Supply (Apotek DMC - Malang)', 'alamat' => 'MALANG'],
            ['nama' => 'Tokopedia - Sentrum Dental', 'alamat' => 'SURABAYA'],
            ['nama' => 'GIGI SURABAYA', 'alamat' => null],
            ['nama' => 'Agung Shopy 69 Surabaya', 'alamat' => 'Surabaya'],
            ['nama' => 'Tokopedia - Sinar Grosir Surabaya', 'alamat' => null],
            ['nama' => 'SQH VENDOR', 'alamat' => null],
            ['nama' => 'PT. Andini Jaya', 'alamat' => 'Jalan NayAus No. 27D, Petojo Utara, Gambir, Jakarta Pusat 10130 Indonesia'],
            ['nama' => 'Anugerah Medika', 'alamat' => null],
            ['nama' => 'Global Teknik Dent', 'alamat' => 'Jl. Raya Candi 3 No. 503'],
            ['nama' => 'Konter', 'alamat' => null], // Nama 'Konter' sangat generik, mungkin perlu diperjelas.
            ['nama' => 'Tokopedia - FDC Dental', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'H&N Gloves', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'Toko Ifa', 'alamat' => null],
            ['nama' => 'Apotik Sehati', 'alamat' => null],
            ['nama' => 'Tokopedia - Laris Store97', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'Tokopedia - MG Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - Jago Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - Champion Medical', 'alamat' => 'Jakarta Barat'],
            ['nama' => 'Tokopedia - OneMed Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - TrustMedica Indonesia', 'alamat' => null],
            ['nama' => 'Tokopedia - Thomasong Dental', 'alamat' => null],
            ['nama' => 'Toko Obat Sumber Waras', 'alamat' => 'Malang'],
            ['nama' => 'Tokopedia - Dentalica', 'alamat' => null],
            ['nama' => 'Tokopedia - Dental Point', 'alamat' => null],
            ['nama' => 'Mitra Usaha - Pengadaan Yohan Yuwono', 'alamat' => null],
            ['nama' => 'Duta Farma', 'alamat' => null],
            ['nama' => 'Tokopedia - Dental Malang Udesign', 'alamat' => null],
            ['nama' => 'Tokopedia - Holy Mart', 'alamat' => null],
            ['nama' => 'Tokopedia - BMS Dentalindo', 'alamat' => null],
            ['nama' => 'PT. Apex Instrumen Husantara', 'alamat' => null],
            ['nama' => 'Permata Dental Laboratory', 'alamat' => null],
            ['nama' => 'Toko ASTAKI', 'alamat' => 'Jl. Raya Tawangmangu Bedak 8A'],
            ['nama' => 'Indonet', 'alamat' => null],
            ['nama' => 'Santosa Houseware', 'alamat' => 'Jl. Laks. Martadinata 74 Kota Malang'],
            ['nama' => 'Tokopedia - GlobalAlkes Store', 'alamat' => null],
            ['nama' => 'Goya', 'alamat' => null], // Nama 'Goya' sangat generik
            ['nama' => 'CV. Prima Abadi Dentalindo', 'alamat' => 'Jl. K.H. Hasyim AshariRuko Roxy Mas Blok B2 No. 30Jakarta Pusat 10150(Sebelah Bank Mega, seberang PT. Inti Karya)'],
            ['nama' => 'Tokopedia - Sejahtera Bersama Grosir', 'alamat' => null],
            ['nama' => 'Tokopedia - Global Dental Supply Malang', 'alamat' => null],
            ['nama' => 'Toko Maju Jaya', 'alamat' => null],
            ['nama' => 'DGDENT Supplies', 'alamat' => null],
            ['nama' => 'Tokopedia - Dentrum Dental', 'alamat' => null], // Kemungkinan duplikat dengan "Tokopedia - Sentrum Dental"
            ['nama' => 'Onemed - Medicom', 'alamat' => null],
            ['nama' => 'Tokopedia - AMZ Gloves', 'alamat' => null],
            ['nama' => 'PT. Pasifik Cita Retailindo', 'alamat' => null],
            ['nama' => 'Jaya Agung Mas Steel', 'alamat' => null],
            ['nama' => 'Ady Wibowo', 'alamat' => null],
            ['nama' => 'Safeglove Tokopedia', 'alamat' => null],
            ['nama' => 'Tokopedia - USA Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - TrustMedica Indonesia - Jakarta Barat', 'alamat' => 'Jakarta Barat'], // Lebih spesifik dari yang di atas
            ['nama' => 'Tokopedia - Anugerah Sukses Sejati', 'alamat' => null],
            ['nama' => 'Ar - Riza Dental Equipment', 'alamat' => 'Jln. Satsui Tubun No. 8A,Kelurahan Kebonsari, Kecamatan Sukun, Kota Malang'],
            ['nama' => 'PT. Global Medik Persada', 'alamat' => null],
            ['nama' => 'Malang Dental Laboratory', 'alamat' => null],
            ['nama' => 'Dental Grosir Malang', 'alamat' => 'Perum Graha Gilang Purnama C4, Baran Gerdrri, Kedungkajar, Pakis, Malang'],
            ['nama' => 'Siswoyo Tekno', 'alamat' => null],
            ['nama' => 'Toko Obat Menang Jaya', 'alamat' => null],
            ['nama' => 'MDI Dental', 'alamat' => null],
            ['nama' => 'Tokopedia - Sahabat Medica Surabaya', 'alamat' => null],
            ['nama' => 'Shopee - Lion Dental', 'alamat' => null],
            ['nama' => 'Shopee - Dunia Dental Shop', 'alamat' => null],
        ];

        $defaultNomorTelepon = 'Tidak Ada Informasi';
        $defaultEmail = 'Tidak Ada Informasi';

        foreach ($suppliers as $supplierData) {
            // Gunakan firstOrCreate untuk menghindari duplikasi berdasarkan nama supplier
            Supplier::firstOrCreate(
                ['nama' => $supplierData['nama']],
                [
                    'alamat' => $supplierData['alamat'],
                    'nomor_telepon' => $supplierData['nomor_telepon'] ?? $defaultNomorTelepon,
                    'email' => $supplierData['email'] ?? $defaultEmail
                ]
            );
        }
    }
}
