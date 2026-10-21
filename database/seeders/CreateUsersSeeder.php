<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userInfo = json_encode([
            'gender' => 'Male',
            'blood_group' => 'A+',
            'birthday' => strtotime('1990-01-01'),
            'phone' => '01700000000',
            'address' => 'Superadmin Address',
            'photo' => '',
        ]);

        $users = [
            [
                'name' => 'Superadmin',
                'email' => 'superadmin@example.com',
                'role_id' => '1',
                'code' => 'SUP001',
                'password' => bcrypt('1234'),
                'user_information' => $userInfo,
                'status' => 1,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'role_id' => '2',
                'code' => 'ADM001',
                'password' => bcrypt('1234'),
                'user_information' => $userInfo,
                'status' => 1,
            ],
            [
                'name' => 'User',
                'email' => 'student@example.com',
                'role_id' => '3',
                'code' => 'USR001',
                'password' => bcrypt('1234'),
                'user_information' => $userInfo,
                'status' => 1,
            ],
        ];

        foreach ($users as $value) {
            User::firstOrCreate(
                ['email' => $value['email']],
                $value
            );
        }
    }
}
