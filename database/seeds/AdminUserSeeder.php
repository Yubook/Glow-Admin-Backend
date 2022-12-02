<?php

use App\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'role_id' => 1,
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'mobile' => '9999999999',
            'address_line_1'=> '33 Clasper Way HERTINGFORDBURY SG14 9UA',
            'profile_approved'=> 1
        ]);
    }
}
