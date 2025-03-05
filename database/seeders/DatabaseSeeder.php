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
use App\Models\Reservation;
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

        Patient::create([
            'name' => 'yoli',
            'email' => 'emailnyayoli@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345'),
            'nomor_telepon' => '081230333587'
        ]);
        Patient::create([
            'name' => 'pasien1',
            'email' => 'pasien1@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('pasien'),
            'nomor_telepon' => '1479575675367'
        ]);
        Patient::create([
            'name' => 'pasien2',
            'email' => 'pasien2@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('pasien'),
            'nomor_telepon' => '89786785'
        ]);
        Patient::create([
            'name' => 'pasien3',
            'email' => 'pasien3@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('pasien'),
            'nomor_telepon' => '0000000'
        ]);

        ScheduleTemplate::create([
            'doctor_id' => 23,
            'day_of_week' => 'Monday',
            'start_time' => '10:00:00',
            'end_time' => '17:00:00',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

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

        Procedure::create([
            'name' => 'Tambal Gigi',
            'description' => 'Prosedur untuk menambal gigi yang berlubang menggunakan bahan tambalan seperti resin komposit.',
        ]);

        Procedure::create([
            'name' => 'Pembersihan Karang Gigi',
            'description' => 'Prosedur untuk membersihkan karang gigi dan plak dengan scaler.',
        ]);

        Procedure::create([
            'name' => 'Pencabutan Gigi',
            'description' => 'Prosedur untuk mencabut gigi yang tidak bisa diselamatkan.',
        ]);

        $procedureMaterials = [
            // Tambal Gigi
            ['procedure_id' => 1, 'dental_material_id' => 1, 'quantity' => 2], // Resin Komposit
            ['procedure_id' => 1, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi
            ['procedure_id' => 1, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
            ['procedure_id' => 1, 'dental_material_id' => 4, 'quantity' => 1], // Amalgam (optional)

            // Pencabutan Gigi
            ['procedure_id' => 3, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi
            ['procedure_id' => 3, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
            ['procedure_id' => 3, 'dental_material_id' => 1, 'quantity' => 1], // Resin Komposit (pelindung pasca pencabutan)

            // Pembersihan Karang Gigi
            ['procedure_id' => 2, 'dental_material_id' => 3, 'quantity' => 1], // Cavity Liner
            ['procedure_id' => 2, 'dental_material_id' => 2, 'quantity' => 1], // Anestesi (opsional)
            ['procedure_id' => 2, 'dental_material_id' => 4, 'quantity' => 1], // Amalgam (kasus tertentu)
        ];

        // Loop dan buat satu per satu dengan create()
        foreach ($procedureMaterials as $data) {
            ProcedureMaterial::create($data);
        }


        // Tambal Gigi - Base Price
        Pricelist::create([
            'procedure_id' => 1,
            'price' => 300000, // Harga dasar: 300.000
            'is_promo' => false,
            'effective_date' => Carbon::now()->subDays(5),
        ]);

        // Tambal Gigi - Promo Price
        Pricelist::create([
            'procedure_id' => 1,
            'price' => 200000, // Harga promosi: 200.000
            'is_promo' => true,
            'effective_date' => Carbon::now()->subDays(2),
        ]);

        // Pembersihan Karang Gigi - Base Price
        Pricelist::create([
            'procedure_id' => 2,
            'price' => 400000, // Harga dasar: 400.000
            'is_promo' => false,
            'effective_date' => Carbon::now()->subDays(10),
        ]);

        // Pembersihan Karang Gigi - Promo Price
        Pricelist::create([
            'procedure_id' => 2,
            'price' => 300000, // Harga promosi: 300.000
            'is_promo' => true,
            'effective_date' => Carbon::now()->subDays(3),
        ]);

        // Pencabutan Gigi - Base Price
        Pricelist::create([
            'procedure_id' => 3,
            'price' => 500000, // Harga dasar: 500.000
            'is_promo' => false,
            'effective_date' => Carbon::now()->subDays(7),
        ]);

        // Pencabutan Gigi - Promo Price
        Pricelist::create([
            'procedure_id' => 3,
            'price' => 350000, // Harga promosi: 350.000
            'is_promo' => true,
            'effective_date' => Carbon::now()->subDays(1),
        ]);

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

            // Piutang Usaha
            ['code' => '1-10100', 'name' => 'Piutang Usaha', 'type' => 'asset'],

            // Persediaan
            //ini 12 13
            ['code' => '1-10200', 'name' => 'Persediaan Barang', 'type' => 'asset'],
            ['code' => '1-10201', 'name' => 'Persediaan Barang Medis', 'type' => 'asset'],

            // Kewajiban (Liabilities)
            ['code' => '2-10001', 'name' => 'Utang Usaha', 'type' => 'liability'],
            ['code' => '2-10002', 'name' => 'Utang Pajak', 'type' => 'liability'],

            // Ekuitas (Equity)
            ['code' => '3-10001', 'name' => 'Modal Pemilik', 'type' => 'equity'],
            ['code' => '3-10002', 'name' => 'Laba Ditahan', 'type' => 'equity'],

            // Pendapatan (Revenue)
            ['code' => '4-10001', 'name' => 'Pendapatan Penjualan', 'type' => 'revenue'],

            // Beban (Expenses)
            ['code' => '5-10001', 'name' => 'Beban Gaji', 'type' => 'expense'],
            ['code' => '5-10002', 'name' => 'HPP Bahan Dental', 'type' => 'expense'],
            ['code' => '5-10003', 'name' => 'Beban Sewa', 'type' => 'expense'],
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

        Reservation::create([
            'patient_id' => 1,
            'doctor_id' => 23,
            'tanggal_reservasi' => '2025-01-23',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00'
        ]);
        Reservation::create([
            'patient_id' => 1,
            'doctor_id' => 24,
            'tanggal_reservasi' => '2025-01-24',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:00'
        ]);
        Reservation::create([
            'patient_id' => 1,
            'doctor_id' => 25,
            'tanggal_reservasi' => '2025-01-25',
            'jam_mulai' => '12:00',
            'jam_selesai' => '13:00'
        ]);
    }
}
