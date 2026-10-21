<?php

namespace Tests\Feature;

use App\Http\Controllers\SchoolAttendanceController;
use App\Http\Controllers\SchoolGradebookController;
use App\Http\Controllers\SchoolRoutineController;
use App\Http\Controllers\SchoolSyllabusController;
use App\Models\School;
use App\Models\Session;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicPhaseDirectoryDesignTest extends TestCase
{
    private ConnectionInterface $tenantConnection;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $tenant=Tenant::query()->first();
        if($tenant===null){$this->markTestSkipped('A tenant is required.');}
        tenancy()->initialize($tenant);
        $this->tenantConnection=DB::connection();
        $this->tenantConnection->beginTransaction();
        $schoolId=(int)School::query()->value('id');
        if($schoolId===0){$this->markTestSkipped('A school is required.');}
        $session=Session::query()->create(['session_title'=>'Design '.Str::random(6),'status'=>1,'school_id'=>$schoolId]);
        School::query()->whereKey($schoolId)->update(['running_session'=>$session->id]);
        $this->admin=User::query()->create(['name'=>'Design Admin','email'=>Str::random(12).'@example.test','password'=>bcrypt('Password123'),'role_id'=>2,'school_id'=>$schoolId,'school_role'=>1,'account_status'=>'enable','status'=>1,'user_information'=>json_encode(['photo'=>''])]);
        Auth::login($this->admin);
    }

    protected function tearDown(): void
    {
        Auth::logout();
        if(isset($this->tenantConnection)&&$this->tenantConnection->transactionLevel()>0){$this->tenantConnection->rollBack();}
        if(tenancy()->initialized){tenancy()->end();}
        parent::tearDown();
    }

    public function test_remaining_academic_pages_use_the_responsive_directory_design(): void
    {
        foreach([
            SchoolSyllabusController::class,
            SchoolRoutineController::class,
            SchoolAttendanceController::class,
            SchoolGradebookController::class,
        ] as $controller){
            $request=Request::create('/admin/test','GET');
            $request->setUserResolver(fn():User=>$this->admin);
            $html=app($controller)->index($request)->render();
            $this->assertStringContainsString('academic-page__summary',$html);
            $this->assertStringContainsString('academic-page__filters',$html);
            $this->assertStringContainsString('school-academic-management.css',$html);
            $this->assertStringContainsString('school-academic-management.js',$html);
        }
    }
}
