<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\School;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $school = School::firstOrCreate(
            ['email' => 'school@demo.com'],
            [
                'title' => 'Demo International School',
                'phone' => '01700000000',
                'address' => '123, Demo Street, Dhaka',
                'school_info' => 'A demo school for testing UI',
                'status' => 1,
                'running_session' => 1,
            ]
        );
        $schoolId = $school->id;

        User::whereIn('email', ['admin@example.com', 'student@example.com', 'superadmin@example.com'])
            ->whereNull('school_id')
            ->update(['school_id' => $schoolId]);

        $session = DB::table('sessions')->where('session_title', '2026-2027')
            ->where('school_id', $schoolId)->value('id');
        if (!$session) {
            $session = DB::table('sessions')->insertGetId([
                'session_title' => '2026-2027',
                'status' => 1,
                'school_id' => $schoolId,
            ]);
        }

        DB::table('global_settings')->where('key', 'running_session')->update(['value' => $session]);

        $classNames = ['One', 'Two', 'Three', 'Four', 'Five'];
        $classIds = [];
        foreach ($classNames as $name) {
            $class = DB::table('classes')->where('name', $name)->where('school_id', $schoolId)->first();
            if (!$class) {
                $classIds[$name] = DB::table('classes')->insertGetId([
                    'name' => $name,
                    'school_id' => $schoolId,
                ]);
            } else {
                $classIds[$name] = $class->id;
            }
        }

        $sectionIds = [];
        foreach ($classIds as $className => $classId) {
            foreach (['A', 'B'] as $sec) {
                $section = DB::table('sections')->where('name', $sec)->where('class_id', $classId)->first();
                if (!$section) {
                    $sectionIds[$className][$sec] = DB::table('sections')->insertGetId([
                        'name' => $sec,
                        'class_id' => $classId,
                        'school_id' => $schoolId,
                    ]);
                } else {
                    $sectionIds[$className][$sec] = $section->id;
                }
            }
        }

        $subjectNames = ['Mathematics', 'English', 'Bangla', 'Science', 'Drawing', 'Religion', 'Physical Education'];
        foreach ($classIds as $className => $classId) {
            foreach ($subjectNames as $sub) {
                DB::table('subjects')->updateOrInsert(
                    ['name' => $sub, 'class_id' => $classId, 'school_id' => $schoolId, 'session_id' => $session],
                    ['name' => $sub, 'class_id' => $classId, 'school_id' => $schoolId, 'session_id' => $session]
                );
            }
        }

        $adminInfo = json_encode([
            'gender' => 'Male',
            'blood_group' => 'A+',
            'birthday' => strtotime('1990-01-01'),
            'phone' => '01711111111',
            'address' => 'Admin Office, Demo School',
            'photo' => '',
        ]);
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('1234'),
                'role_id' => 2,
                'school_id' => $schoolId,
                'code' => 'ADM001',
                'user_information' => $adminInfo,
                'status' => 1,
            ]
        );

        $teachers = [
            ['name' => 'Mr. Rahman', 'email' => 'teacher1@demo.com', 'gender' => 'Male', 'designation' => 'Senior Teacher'],
            ['name' => 'Mrs. Sultana', 'email' => 'teacher2@demo.com', 'gender' => 'Female', 'designation' => 'Math Teacher'],
            ['name' => 'Mr. Hossain', 'email' => 'teacher3@demo.com', 'gender' => 'Male', 'designation' => 'Science Teacher'],
        ];
        $teacherIds = [];
        foreach ($teachers as $t) {
            $info = json_encode([
                'gender' => $t['gender'],
                'blood_group' => 'B+',
                'birthday' => strtotime('1985-06-15'),
                'phone' => '017' . rand(10000000, 99999999),
                'address' => 'Teacher Residence',
                'photo' => '',
            ]);
            $user = User::firstOrCreate(
                ['email' => $t['email']],
                [
                    'name' => $t['name'],
                    'password' => Hash::make('1234'),
                    'role_id' => 3,
                    'school_id' => $schoolId,
                    'code' => 'TCH' . str_pad(count($teacherIds) + 1, 3, '0', STR_PAD_LEFT),
                    'user_information' => $info,
                    'status' => 1,
                    'designation' => $t['designation'],
                    'language' => 'english',
                ]
            );
            $teacherIds[] = $user->id;
        }

        $parents = [
            ['name' => 'Mr. Kamal', 'email' => 'parent1@demo.com', 'gender' => 'Male'],
            ['name' => 'Mrs. Jahanara', 'email' => 'parent2@demo.com', 'gender' => 'Female'],
            ['name' => 'Mr. Alam', 'email' => 'parent3@demo.com', 'gender' => 'Male'],
            ['name' => 'Mrs. Hosneara', 'email' => 'parent4@demo.com', 'gender' => 'Female'],
            ['name' => 'Mr. Kabir', 'email' => 'parent5@demo.com', 'gender' => 'Male'],
        ];
        $parentIds = [];
        foreach ($parents as $p) {
            $info = json_encode([
                'gender' => $p['gender'],
                'blood_group' => 'A-',
                'birthday' => strtotime('1980-01-01'),
                'phone' => '017' . rand(10000000, 99999999),
                'address' => 'Parent Address',
                'photo' => '',
            ]);
            $user = User::firstOrCreate(
                ['email' => $p['email']],
                [
                    'name' => $p['name'],
                    'password' => Hash::make('1234'),
                    'role_id' => 6,
                    'school_id' => $schoolId,
                    'code' => 'PAR' . str_pad(count($parentIds) + 1, 3, '0', STR_PAD_LEFT),
                    'user_information' => $info,
                    'status' => 1,
                    'language' => 'english',
                ]
            );
            $parentIds[] = $user->id;
        }

        $studentData = [
            ['name' => 'Rahim', 'class' => 'One', 'section' => 'A', 'parentIdx' => 0],
            ['name' => 'Karim', 'class' => 'One', 'section' => 'B', 'parentIdx' => 1],
            ['name' => 'Fatima', 'class' => 'Two', 'section' => 'A', 'parentIdx' => 2],
            ['name' => 'Ayesha', 'class' => 'Two', 'section' => 'B', 'parentIdx' => 3],
            ['name' => 'Sumon', 'class' => 'Three', 'section' => 'A', 'parentIdx' => 4],
            ['name' => 'Shahin', 'class' => 'Three', 'section' => 'B', 'parentIdx' => 0],
            ['name' => 'Nusrat', 'class' => 'Four', 'section' => 'A', 'parentIdx' => 1],
            ['name' => 'Tania', 'class' => 'Four', 'section' => 'B', 'parentIdx' => 2],
            ['name' => 'Rafiq', 'class' => 'Five', 'section' => 'A', 'parentIdx' => 3],
            ['name' => 'Sabbir', 'class' => 'Five', 'section' => 'B', 'parentIdx' => 4],
        ];

        $genderMap = ['Rahim' => 'Male', 'Karim' => 'Male', 'Fatima' => 'Female', 'Ayesha' => 'Female', 'Sumon' => 'Male', 'Shahin' => 'Male', 'Nusrat' => 'Female', 'Tania' => 'Female', 'Rafiq' => 'Male', 'Sabbir' => 'Male'];
        $idx = 1;
        foreach ($studentData as $s) {
            $gender = $genderMap[$s['name']];
            $email = strtolower($s['name']) . '@demo.com';
            $info = json_encode([
                'gender' => $gender,
                'blood_group' => 'O+',
                'birthday' => strtotime('2010-01-01'),
                'phone' => '017' . rand(10000000, 99999999),
                'address' => 'Student Address',
                'photo' => '',
            ]);
            $student = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('1234'),
                    'role_id' => 7,
                    'school_id' => $schoolId,
                    'parent_id' => $parentIds[$s['parentIdx']],
                    'code' => 'STU' . str_pad($idx, 3, '0', STR_PAD_LEFT),
                    'user_information' => $info,
                    'status' => 1,
                    'language' => 'english',
                ]
            );
            DB::table('enrollments')->updateOrInsert(
                ['user_id' => $student->id, 'session_id' => $session],
                ['class_id' => $classIds[$s['class']], 'section_id' => $sectionIds[$s['class']][$s['section']], 'school_id' => $schoolId]
            );
            $idx++;
        }

        echo "Demo data seeded successfully!\n";
        echo "School: Demo International School\n";
        echo "Admin: admin@example.com / 1234\n";
        echo "Teachers: teacher1@demo.com, teacher2@demo.com, teacher3@demo.com / 1234\n";
        echo "Parents: parent1@demo.com ~ parent5@demo.com / 1234\n";
        echo "Students: rahim@demo.com, karim@demo.com, ... / 1234\n";
    }
}
