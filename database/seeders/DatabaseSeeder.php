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

        $patients = [
            [
                'name' => 'Yoli',
                'email' => 'emailnyayoli@gmail.com',
                'password' => Hash::make('12345'),
                'home_mobile' => '081230333587',
            ],
            [
                'name' => 'Pasien1',
                'email' => 'pasien1@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '1479575675367',
            ],
            [
                'name' => 'Pasien2',
                'email' => 'pasien2@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '89786785',
            ],
            [
                'name' => 'Pasien3',
                'email' => 'pasien3@gmail.com',
                'password' => Hash::make('pasien'),
                'home_mobile' => '0000000',
            ],
        ];

        foreach ($patients as $data) {
            // Generate Patient ID
            $initialLetter = strtoupper(substr($data['name'], 0, 1));
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
