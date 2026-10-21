<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
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

        SuperAdmin::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make('1234'),
                'user_information' => $userInfo,
            ]
        );
    }
}
