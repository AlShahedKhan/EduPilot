<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use LogicException;

class TemporminusetnonTeacherSeeder extends Seeder
{
    public function run(): void
    {
        if (! tenancy()->initialized || tenant('id') !== 'temporminusetnon') {
            throw new LogicException('This seeder may only run for the temporminusetnon tenant.');
        }

        $school = School::query()->firstOrFail();

        foreach ($this->teachers() as $index => $teacherData) {
            $department = Department::query()->firstOrCreate([
                'name' => $teacherData['department'],
                'school_id' => $school->id,
            ]);

            $teacher = User::query()->firstOrNew(['email' => $teacherData['email']]);

            if (! $teacher->exists) {
                $teacher->password = Hash::make('1234');
            }

            $teacher->fill([
                'name' => $teacherData['name'],
                'role_id' => 3,
                'school_id' => $school->id,
                'code' => 'TCH'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'user_information' => json_encode([
                    'gender' => $teacherData['gender'],
                    'blood_group' => $teacherData['blood_group'],
                    'birthday' => strtotime($teacherData['birthday']),
                    'phone' => $teacherData['phone'],
                    'address' => $school->address ?: $school->title,
                    'photo' => '',
                ], JSON_THROW_ON_ERROR),
                'status' => 1,
                'department_id' => $department->id,
                'designation' => $teacherData['designation'],
                'language' => 'english',
                'account_status' => 'enable',
            ]);
            $teacher->save();
        }
    }

    /**
     * @return array<int, string>
     */
    public static function teacherEmails(): array
    {
        return array_column((new self())->teachers(), 'email');
    }

    /**
     * @return array<int, array{name: string, email: string, gender: string, blood_group: string, birthday: string, phone: string, designation: string, department: string}>
     */
    private function teachers(): array
    {
        return [
            [
                'name' => 'Amina Rahman',
                'email' => 'amina.rahman@mackensie.test',
                'gender' => 'Female',
                'blood_group' => 'A+',
                'birthday' => '1986-04-12',
                'phone' => '01710000001',
                'designation' => 'Head of Mathematics',
                'department' => 'Mathematics',
            ],
            [
                'name' => 'Farhan Ahmed',
                'email' => 'farhan.ahmed@mackensie.test',
                'gender' => 'Male',
                'blood_group' => 'B+',
                'birthday' => '1988-09-23',
                'phone' => '01710000002',
                'designation' => 'Senior English Teacher',
                'department' => 'English',
            ],
            [
                'name' => 'Nusrat Jahan',
                'email' => 'nusrat.jahan@mackensie.test',
                'gender' => 'Female',
                'blood_group' => 'O+',
                'birthday' => '1990-01-17',
                'phone' => '01710000003',
                'designation' => 'Science Teacher',
                'department' => 'Science',
            ],
            [
                'name' => 'Imran Hossain',
                'email' => 'imran.hossain@mackensie.test',
                'gender' => 'Male',
                'blood_group' => 'AB+',
                'birthday' => '1987-07-08',
                'phone' => '01710000004',
                'designation' => 'ICT Teacher',
                'department' => 'Information Technology',
            ],
            [
                'name' => 'Sabina Akter',
                'email' => 'sabina.akter@mackensie.test',
                'gender' => 'Female',
                'blood_group' => 'A-',
                'birthday' => '1989-11-30',
                'phone' => '01710000005',
                'designation' => 'Humanities Teacher',
                'department' => 'Humanities',
            ],
        ];
    }
}
