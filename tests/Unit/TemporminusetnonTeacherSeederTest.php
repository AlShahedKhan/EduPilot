<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TemporminusetnonTeacherSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\ConnectionInterface;
use PHPUnit\Framework\TestCase;

class TemporminusetnonTeacherSeederTest extends TestCase
{
    private ConnectionInterface $tenantConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        tenancy()->initialize(Tenant::findOrFail('temporminusetnon'));

        $this->tenantConnection = app('db')->connection();
        $this->tenantConnection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->tenantConnection->rollBack();
        tenancy()->end();
        restore_error_handler();
        restore_exception_handler();

        parent::tearDown();
    }

    public function test_seeder_creates_exactly_five_idempotent_active_teachers(): void
    {
        $seeder = app(TemporminusetnonTeacherSeeder::class);

        $seeder->run();
        $seeder->run();

        $teachers = User::query()
            ->whereIn('email', TemporminusetnonTeacherSeeder::teacherEmails())
            ->get();

        $this->assertCount(5, $teachers);
        $this->assertSame(5, $teachers->pluck('email')->unique()->count());
        $this->assertTrue($teachers->every(
            fn (User $teacher): bool => (int) $teacher->role_id === 3
                && (int) $teacher->status === 1
                && $teacher->school_id !== null
                && $teacher->department_id !== null
                && filled($teacher->designation)
        ));
        $this->assertSame(5, Department::query()->whereIn('id', $teachers->pluck('department_id'))->count());
    }
}
