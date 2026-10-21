<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class TemporminusetnonStudentSeeder extends Seeder
{
    public function run(): void
    {
        if (! tenancy()->initialized || tenant('id') !== 'temporminusetnon') {
            throw new LogicException('This seeder may only run for the temporminusetnon tenant.');
        }

        $school = School::query()->firstOrFail();

        if (User::query()->where('school_id', $school->id)->where('role_id', 7)->exists()) {
            return;
        }

        foreach ($this->students() as $index => $studentData) {
            User::query()->create([
                'name' => $studentData['name'],
                'email' => $studentData['email'],
                'password' => Hash::make('1234'),
                'role_id' => 7,
                'school_id' => $school->id,
                'code' => 'STU'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'user_information' => json_encode([
                    'gender' => $studentData['gender'],
                    'blood_group' => $studentData['blood_group'],
                    'birthday' => strtotime($studentData['birthday']),
                    'phone' => $studentData['phone'],
                    'address' => $school->address ?: $school->title,
                    'photo' => '',
                ], JSON_THROW_ON_ERROR),
                'status' => 1,
                'language' => 'english',
                'account_status' => 'enable',
            ]);
        }
    }

    /**
     * @return array<int, array{name: string, email: string, gender: string, blood_group: string, birthday: string, phone: string}>
     */
    private function students(): array
    {
        return [
            ['name' => 'Adiba Noor', 'email' => 'adiba.noor@mackensie.test', 'gender' => 'Female', 'blood_group' => 'A+', 'birthday' => '2012-02-14', 'phone' => '01810000101'],
            ['name' => 'Arham Hasan', 'email' => 'arham.hasan@mackensie.test', 'gender' => 'Male', 'blood_group' => 'B+', 'birthday' => '2011-06-21', 'phone' => '01810000102'],
            ['name' => 'Tasnim Islam', 'email' => 'tasnim.islam@mackensie.test', 'gender' => 'Female', 'blood_group' => 'O+', 'birthday' => '2012-09-05', 'phone' => '01810000103'],
            ['name' => 'Rayyan Ahmed', 'email' => 'rayyan.ahmed@mackensie.test', 'gender' => 'Male', 'blood_group' => 'AB+', 'birthday' => '2011-11-18', 'phone' => '01810000104'],
            ['name' => 'Nafisa Rahman', 'email' => 'nafisa.rahman@mackensie.test', 'gender' => 'Female', 'blood_group' => 'A-', 'birthday' => '2012-04-27', 'phone' => '01810000105'],
            ['name' => 'Zayan Hossain', 'email' => 'zayan.hossain@mackensie.test', 'gender' => 'Male', 'blood_group' => 'O-', 'birthday' => '2011-08-09', 'phone' => '01810000106'],
            ['name' => 'Meher Jahan', 'email' => 'meher.jahan@mackensie.test', 'gender' => 'Female', 'blood_group' => 'B-', 'birthday' => '2012-01-31', 'phone' => '01810000107'],
            ['name' => 'Ayman Kabir', 'email' => 'ayman.kabir@mackensie.test', 'gender' => 'Male', 'blood_group' => 'A+', 'birthday' => '2011-05-16', 'phone' => '01810000108'],
            ['name' => 'Inaya Chowdhury', 'email' => 'inaya.chowdhury@mackensie.test', 'gender' => 'Female', 'blood_group' => 'O+', 'birthday' => '2012-07-23', 'phone' => '01810000109'],
            ['name' => 'Samiul Karim', 'email' => 'samiul.karim@mackensie.test', 'gender' => 'Male', 'blood_group' => 'B+', 'birthday' => '2011-12-12', 'phone' => '01810000110'],
        ];
    }
}
