<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Patient;
use App\Models\Schedules;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

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
            'password' => bcrypt('12345678'),
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
            'is_available' => true,
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
            'jam_reservasi' => '09:00:00',
        ]);

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