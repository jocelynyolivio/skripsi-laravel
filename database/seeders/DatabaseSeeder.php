<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\Patient;
use App\Models\Category;
use App\Models\Procedure;
use App\Models\Schedules;
use App\Models\Odontogram;
use App\Models\Reservation;
use App\Models\DentalMaterial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::create([
            'name' => 'Jocelyn',
            'email' => 'yoli1@gmail.com',
            'password' => bcrypt('12345'),
            'role_id' => 1
        ]);

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
            'role_name' => 'dokter'
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

        Schedules::create([
            'doctor_id' => 3, // Sesuaikan ID dokter
            'date' => '2024-01-01',
            'time_start' => '09:00:00',
            'time_end' => '10:00:00',
            'is_available' => false,
        ]);
        Schedules::create([
            'doctor_id' => 3, // Sesuaikan ID dokter
            'date' => '2023-12-12',
            'time_start' => '08:00:00',
            'time_end' => '10:00:00',
            'is_available' => true,
        ]);

            Schedules::create([
                'doctor_id' => 4, // Sesuaikan ID dokter
                'date' => '2025-08-04',
                'time_start' => '11:00:00',
                'time_end' => '14:00:00',
                'is_available' => true,
            ]);

        Schedules::create([
            'doctor_id' => 5, // Sesuaikan ID dokter
            'date' => '2024-12-01',
            'time_start' => '19:00:00',
            'time_end' => '20:00:00',
            'is_available' => true,
        ]);

        Reservation::create([
            'schedule_id' => 1,
            'patient_id' => 1,
            'doctor_id' => 3,
            'tanggal_reservasi' => '2024-01-01',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:00:00',
        ]);
        DentalMaterial::create([
            'name' => 'Resin Komposit',
            'description' => 'Bahan untuk penambalan gigi berlubang',
            'stock_quantity' => 50,
            'unit_price' => 150.00,
        ]);

        DentalMaterial::create([
            'name' => 'Anestesi',
            'description' => 'Cairan anestesi lokal untuk prosedur dental',
            'stock_quantity' => 30,
            'unit_price' => 25.00,
        ]);

        DentalMaterial::create([
            'name' => 'Cavity Liner',
            'description' => 'Bahan pelapis untuk restorasi tambalan',
            'stock_quantity' => 20,
            'unit_price' => 45.00,
        ]);

        DentalMaterial::create([
            'name' => 'Amalgam',
            'description' => 'Bahan tambalan yang mengandung logam',
            'stock_quantity' => 10,
            'unit_price' => 100.00,
        ]);

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

                // Menghubungkan Tambal Gigi dengan beberapa bahan dental
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 1, // ID untuk Tambal Gigi
                    'dental_material_id' => 1, // Resin Komposit
                    'quantity' => 2, // Butuh 2 unit Resin Komposit
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 1, // ID untuk Tambal Gigi
                    'dental_material_id' => 2, // Anestesi
                    'quantity' => 1, // Butuh 1 unit Anestesi
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 1, // ID untuk Tambal Gigi
                    'dental_material_id' => 3, // Cavity Liner
                    'quantity' => 1, // Butuh 1 unit Cavity Liner
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 1, // ID untuk Tambal Gigi
                    'dental_material_id' => 4, // Amalgam
                    'quantity' => 1, // Butuh 1 unit Amalgam (optional untuk kombinasi bahan)
                ]);
        
                // Menghubungkan Pencabutan Gigi dengan beberapa bahan dental
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 3, // ID untuk Pencabutan Gigi
                    'dental_material_id' => 2, // Anestesi
                    'quantity' => 1, // Butuh 1 unit Anestesi
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 3, // ID untuk Pencabutan Gigi
                    'dental_material_id' => 3, // Cavity Liner
                    'quantity' => 1, // Butuh 1 unit Cavity Liner untuk perlindungan
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 3, // ID untuk Pencabutan Gigi
                    'dental_material_id' => 1, // Resin Komposit (untuk pelindung gigi pasca pencabutan)
                    'quantity' => 1, // Butuh 1 unit Resin Komposit
                ]);
        
                // Menghubungkan Pembersihan Karang Gigi dengan beberapa bahan dental
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 2, // ID untuk Pembersihan Karang Gigi
                    'dental_material_id' => 3, // Cavity Liner
                    'quantity' => 1, // Butuh 1 unit Cavity Liner
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 2, // ID untuk Pembersihan Karang Gigi
                    'dental_material_id' => 2, // Anestesi (opsional jika pasien mengalami ketidaknyamanan)
                    'quantity' => 1, // Butuh 1 unit Anestesi
                ]);
        
                DB::table('procedure_materials')->insert([
                    'procedure_id' => 2, // ID untuk Pembersihan Karang Gigi
                    'dental_material_id' => 4, // Amalgam (untuk kasus tertentu dengan tambalan kecil)
                    'quantity' => 1, // Butuh 1 unit Amalgam
                ]);


        // Odontogram::create([
        //     'medical_record_id' => 1, // Sesuaikan ID rekam medis yang sudah ada
        //     'tooth_number' => '11', // Gigi 11 (Insisivus kanan atas)
        //     'status' => 'sehat', // Status gigi sehat
        //     'notes' => 'Tidak ada keluhan', // Catatan
        // ]);

        // Odontogram::create([
        //     'medical_record_id' => 1,
        //     'tooth_number' => '12', // Gigi 12
        //     'status' => 'berlubang', // Status gigi berlubang
        //     'notes' => 'Karies kecil pada email, butuh observasi',
        // ]);

        // Odontogram::create([
        //     'medical_record_id' => 2, // Sesuaikan ID rekam medis
        //     'tooth_number' => '21', // Gigi 21 (Insisivus kiri atas)
        //     'status' => 'tambalan', // Status gigi tambalan
        //     'notes' => 'Tambalan komposit resin', // Catatan
        // ]);

        // Odontogram::create([
        //     'medical_record_id' => 3, // Sesuaikan ID rekam medis
        //     'tooth_number' => '36', // Gigi 36 (Molari kiri bawah)
        //     'status' => 'berlubang', // Status gigi berlubang
        //     'notes' => 'Karies parah, perlu tindakan endodonti',
        // ]);


        // Post::create([
        //     'title' => 'Judul Pertama',
        //     'slug' => 'judul-pertama',
        //     'category_id' => 1,
        //     'user_id' => 1,
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum et iusto blanditiis eligendi.',
        //     'body' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis quaerat tempore, assumenda labore commodi consequatur officia quibusdam asperiores tenetur, repudiandae nam repellat veniam hic iste error? Voluptatibus cumque sed sit cupiditate rerum, saepe perferendis sint. Nulla sunt facere quaerat tempore nesciunt quis explicabo nemo assumenda quae harum. Labore iste aut reiciendis a! Commodi, cum!</p><p> Laudantium accusantium saepe accusamus nihil consequuntur, neque aliquid, veritatis tempore reiciendis aspernatur alias fugit dolore placeat! Accusantium, sequi? Ex, voluptate quos, in enim totam soluta sed quas ullam nihil necessitatibus ipsa earum rem nulla perferendis amet possimus expedita officiis, vel libero odit dolorem. Ad aliquid nobis vel aspernatur voluptatem nam nesciunt minima sunt enim consequatur expedita, ipsa fugit tempora dolore voluptas quis explicabo quo eius.</p><p> Quos, amet, reiciendis corrupti quia qui harum rem quibusdam rerum consectetur, natus voluptatum unde labore doloremque accusamus ullam iure repellat? Unde placeat ipsam harum fugiat! Delectus minima sed velit dolore rem illum voluptate quasi facilis tenetur, rerum sequi voluptatibus similique dolorem adipisci nobis possimus accusamus beatae eos nam ipsum doloribus deserunt alias mollitia? Dignissimos, numquam atque! Laboriosam voluptates esse sapiente vel?</p>'
        // ]);

        // Post::create([
        //     'title' => 'Judul Kedua',
        //     'slug' => 'judul-kedua',
        //     'category_id' => 2,
        //     'user_id' => 1,
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum et iusto blanditiis eligendi.',
        //     'body' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis quaerat tempore, assumenda labore commodi consequatur officia quibusdam asperiores tenetur, repudiandae nam repellat veniam hic iste error? Voluptatibus cumque sed sit cupiditate rerum, saepe perferendis sint. Nulla sunt facere quaerat tempore nesciunt quis explicabo nemo assumenda quae harum. Labore iste aut reiciendis a! Commodi, cum!</p><p> Laudantium accusantium saepe accusamus nihil consequuntur, neque aliquid, veritatis tempore reiciendis aspernatur alias fugit dolore placeat! Accusantium, sequi? Ex, voluptate quos, in enim totam soluta sed quas ullam nihil necessitatibus ipsa earum rem nulla perferendis amet possimus expedita officiis, vel libero odit dolorem. Ad aliquid nobis vel aspernatur voluptatem nam nesciunt minima sunt enim consequatur expedita, ipsa fugit tempora dolore voluptas quis explicabo quo eius.</p><p> Quos, amet, reiciendis corrupti quia qui harum rem quibusdam rerum consectetur, natus voluptatum unde labore doloremque accusamus ullam iure repellat? Unde placeat ipsam harum fugiat! Delectus minima sed velit dolore rem illum voluptate quasi facilis tenetur, rerum sequi voluptatibus similique dolorem adipisci nobis possimus accusamus beatae eos nam ipsum doloribus deserunt alias mollitia? Dignissimos, numquam atque! Laboriosam voluptates esse sapiente vel?</p>'
        // ]);

        // Post::create([
        //     'title' => 'Judul Ketiga',
        //     'slug' => 'judul-ketiga',
        //     'category_id' => 1,
        //     'user_id' => 2,
        //     'excerpt' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Harum et iusto blanditiis eligendi.',
        //     'body' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis quaerat tempore, assumenda labore commodi consequatur officia quibusdam asperiores tenetur, repudiandae nam repellat veniam hic iste error? Voluptatibus cumque sed sit cupiditate rerum, saepe perferendis sint. Nulla sunt facere quaerat tempore nesciunt quis explicabo nemo assumenda quae harum. Labore iste aut reiciendis a! Commodi, cum!</p><p> Laudantium accusantium saepe accusamus nihil consequuntur, neque aliquid, veritatis tempore reiciendis aspernatur alias fugit dolore placeat! Accusantium, sequi? Ex, voluptate quos, in enim totam soluta sed quas ullam nihil necessitatibus ipsa earum rem nulla perferendis amet possimus expedita officiis, vel libero odit dolorem. Ad aliquid nobis vel aspernatur voluptatem nam nesciunt minima sunt enim consequatur expedita, ipsa fugit tempora dolore voluptas quis explicabo quo eius.</p><p> Quos, amet, reiciendis corrupti quia qui harum rem quibusdam rerum consectetur, natus voluptatum unde labore doloremque accusamus ullam iure repellat? Unde placeat ipsam harum fugiat! Delectus minima sed velit dolore rem illum voluptate quasi facilis tenetur, rerum sequi voluptatibus similique dolorem adipisci nobis possimus accusamus beatae eos nam ipsum doloribus deserunt alias mollitia? Dignissimos, numquam atque! Laboriosam voluptates esse sapiente vel?</p>'
        // ]);
    //     User::factory(5)->create(); 
    //     Post::factory(20)->create(); 
    // }
}
}