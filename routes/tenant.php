<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Http\Controllers\AccountantController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Updater;
use App\Http\Controllers\CropController;
use App\Http\Controllers\WardenController;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    \App\Http\Middleware\EnsureSchoolIsActive::class,
    'throttle:60,1',
])->group(function () {

    Route::get('school-inactive', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 1) {
            return redirect('/');
        }
        return view('errors.school-inactive');
    })->name('school.inactive');

    Route::get('/', [\App\Http\Controllers\SchoolLandingController::class, 'index'])->name('school.landing');
    Route::post('contact', [\App\Http\Controllers\SchoolLandingController::class, 'contactStore'])->name('school.contact.store');

    //Auth routes are here
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    //Superadmin impersonation: logs into the school as its admin via a one-time token.
    Route::get('login-as/{token}', [LoginController::class, 'loginAs'])->name('login.as');

    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

    Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    Route::post('password/confirm', [ConfirmPasswordController::class, 'confirm']);

    // Account Disable Route

    Route::get('admin/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.account_disableview');
    })->middleware('auth')->name('admin.account_disableview');

    Route::get('accountant/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect('/');
        }
        return view('accountant.account_disable');
    })->middleware('auth')->name('accountant.account_disable');

    Route::get('teacher/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect()->route('teacher.dashboard');
        }
        return view('teacher.account_disable');
    })->middleware('auth')->name('teacher.account_disable');

    Route::get('librarian/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect('/');
        }
        return view('librarian.account_disable');
    })->middleware('auth')->name('librarian.account_disable');

    Route::get('parent/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect()->route('parent.dashboard');
        }
        return view('parent.account_disable');
    })->middleware('auth')->name('parent.account_disable');

    Route::get('student/account-disable', function () {
        $user = auth()->user();
        if (function_exists('school_status_check') && school_status_check($user->email ?? '') == 0) {
            return view('errors.school-inactive');
        }
        if (!$user || $user->account_status != 'disable') {
            return redirect()->route('student.dashboard');
        }
        return view('student.account_disable');
    })->middleware('auth')->name('student.account_disable');

    //Admin routes are here
    Route::controller(AdminController::class)->middleware('admin', 'auth')->group(function () {

        // Subject management routes precede the legacy plural lookup route below.
        Route::get('admin/subjects/create', [\App\Http\Controllers\SchoolSubjectController::class, 'create'])->name('admin.subject.open_modal');
        Route::get('admin/subjects/export.pdf', [\App\Http\Controllers\SchoolSubjectController::class, 'exportPdf'])->name('admin.subject.export.pdf');
        Route::post('admin/subjects', [\App\Http\Controllers\SchoolSubjectController::class, 'store'])->name('admin.create.subject');
        Route::get('admin/subjects/{subject}/edit', [\App\Http\Controllers\SchoolSubjectController::class, 'edit'])->name('admin.edit.subject');
        Route::patch('admin/subjects/{subject}', [\App\Http\Controllers\SchoolSubjectController::class, 'update'])->name('admin.subject.update');
        Route::delete('admin/subjects/{subject}', [\App\Http\Controllers\SchoolSubjectController::class, 'destroy'])->name('admin.subject.delete');
        Route::get('admin/subjects/classes/{class}/sections', [\App\Http\Controllers\SchoolSubjectController::class, 'sections'])->name('admin.subject.sections');
        Route::get('admin/subjects/classes/{class}/subjects', [\App\Http\Controllers\SchoolSubjectController::class, 'subjects'])->name('admin.subject.options');

        Route::get('admin/contacts', [\App\Http\Controllers\SchoolContactController::class, 'index'])->name('admin.contacts.index');
        Route::get('admin/contacts/{contact}', [\App\Http\Controllers\SchoolContactController::class, 'show'])->name('admin.contacts.show');
        Route::post('admin/contacts/{contact}/status', [\App\Http\Controllers\SchoolContactController::class, 'updateStatus'])->name('admin.contacts.status');

        Route::get('admin/dashboard', 'adminDashboard')->name('admin.dashboard')->middleware('role_id');

        //Common routes
        Route::get('admin/section/{id}', 'classWiseSections')->name('admin.class_wise_sections');
        Route::get('admin/subjects/{id}', 'classWiseSubject')->name('admin.class_wise_subject');
        Route::get('admin/students/{id}', 'classWiseStudents')->name('admin.class_wise_student');
        Route::get('admin/students_invoice/{id}', 'classWiseStudentsInvoice')->name('admin.class_wise_student_invoice');

        //Admin users route
        Route::get('admin/admin', [\App\Http\Controllers\SchoolAdminController::class, 'index'])->name('admin.admin')->middleware('admin_permission');
        Route::get('admin/admin/create_modal', [\App\Http\Controllers\SchoolAdminController::class, 'create'])->name('admin.open_modal');
        Route::post('admin/admin', [\App\Http\Controllers\SchoolAdminController::class, 'store'])->name('admin.create');
        Route::get('admin/admin/{admin}/edit', [\App\Http\Controllers\SchoolAdminController::class, 'edit'])->name('admin.open_edit_modal');
        Route::patch('admin/admin/{admin}', [\App\Http\Controllers\SchoolAdminController::class, 'update'])->name('admin.update');
        Route::delete('admin/admin/{admin}', [\App\Http\Controllers\SchoolAdminController::class, 'destroy'])->name('admin.admin.delete');
        Route::patch('admin/admin/{admin}/status', [\App\Http\Controllers\SchoolAdminController::class, 'updateStatus'])->name('admin.admin.status');
        Route::get('admin/admin/{admin}/profile', [\App\Http\Controllers\SchoolAdminController::class, 'profile'])->name('admin.admin.admin_profile');
        Route::patch('admin/admin/{admin}/password', [\App\Http\Controllers\SchoolAdminController::class, 'updatePassword'])->name('admin.user_password');
        Route::get('admin/admin/{admin}/permissions', [\App\Http\Controllers\SchoolAdminController::class, 'permissions'])->name('admin.admin.menu_permission');
        Route::patch('admin/admin/{admin}/permissions', [\App\Http\Controllers\SchoolAdminController::class, 'updatePermissions'])->name('admin.admin.menu_permission_update');

        Route::get('admin/admin-documents/{id}', 'adminDocuments')->name('admin.documents');
        Route::get('admin/student-documents/{id}', 'studentDocuments')->name('admin.student.documents');
        Route::get('admin/teacher/{teacher}/documents', [\App\Http\Controllers\SchoolTeacherController::class, 'documents'])->name('admin.teacher.documents');

        Route::any('admin/documents-upload/{id}', 'documentsUpload')->name('admin.documents.upload');
        Route::get('admin/documents-remove/{id}/{file_name}', 'documentsRemove')->name('admin.documents.remove');

        // Image crop route
        Route::post('crop-image', [CropController::class, 'crop'])->name('crop.image');

        //Teacher users route
        Route::get('admin/teacher', [\App\Http\Controllers\SchoolTeacherController::class, 'index'])->name('admin.teacher')->middleware('admin_permission');
        Route::get('admin/teacher/create_modal', [\App\Http\Controllers\SchoolTeacherController::class, 'create'])->name('admin.teacher.open_modal');
        Route::post('admin/teacher', [\App\Http\Controllers\SchoolTeacherController::class, 'store'])->name('admin.teacher.create');
        Route::get('admin/teacher/{teacher}/edit', [\App\Http\Controllers\SchoolTeacherController::class, 'edit'])->name('admin.teacher_edit_modal');
        Route::patch('admin/teacher/{teacher}', [\App\Http\Controllers\SchoolTeacherController::class, 'update'])->name('admin.teacher.update');
        Route::delete('admin/teacher/{teacher}', [\App\Http\Controllers\SchoolTeacherController::class, 'destroy'])->name('admin.teacher.delete');
        Route::patch('admin/teacher/{teacher}/status', [\App\Http\Controllers\SchoolTeacherController::class, 'updateStatus'])->name('admin.teacher.status');
        Route::get('admin/teacher/{teacher}/profile', [\App\Http\Controllers\SchoolTeacherController::class, 'profile'])->name('admin.teacher.teacher_profile');
        Route::patch('admin/teacher/{teacher}/password', [\App\Http\Controllers\SchoolTeacherController::class, 'updatePassword'])->name('admin.teacher.password');

        //Accountant users route
        Route::get('admin/accountant', [\App\Http\Controllers\SchoolAccountantController::class, 'index'])->name('admin.accountant')->middleware('admin_permission');
        Route::get('admin/accountant/create_modal', [\App\Http\Controllers\SchoolAccountantController::class, 'create'])->name('admin.accountant.open_modal');
        Route::post('admin/accountant', [\App\Http\Controllers\SchoolAccountantController::class, 'store'])->name('admin.accountant.create');
        Route::get('admin/accountant/{accountant}/edit', [\App\Http\Controllers\SchoolAccountantController::class, 'edit'])->name('admin.accountant_edit_modal');
        Route::patch('admin/accountant/{accountant}', [\App\Http\Controllers\SchoolAccountantController::class, 'update'])->name('admin.accountant.update');
        Route::delete('admin/accountant/{accountant}', [\App\Http\Controllers\SchoolAccountantController::class, 'destroy'])->name('admin.accountant.delete');
        Route::patch('admin/accountant/{accountant}/status', [\App\Http\Controllers\SchoolAccountantController::class, 'updateStatus'])->name('admin.accountant.status');
        Route::patch('admin/accountant/{accountant}/password', [\App\Http\Controllers\SchoolAccountantController::class, 'updatePassword'])->name('admin.accountant.password');
        Route::get('admin/accountant/{accountant}/profile', [\App\Http\Controllers\SchoolAccountantController::class, 'profile'])->name('admin.accountant.accountant_profile');
        Route::get('admin/accountant/{accountant}/documents', [\App\Http\Controllers\SchoolAccountantController::class, 'documents'])->name('admin.accountant.documents');
        Route::post('admin/accountant/{accountant}/documents', [\App\Http\Controllers\SchoolAccountantController::class, 'storeDocument'])->name('admin.accountant.documents.store');
        Route::delete('admin/accountant/{accountant}/documents/{document}', [\App\Http\Controllers\SchoolAccountantController::class, 'destroyDocument'])->name('admin.accountant.documents.destroy');

        //Librarian users route
        Route::get('admin/librarian', [\App\Http\Controllers\SchoolLibrarianController::class, 'index'])->name('admin.librarian')->middleware('admin_permission');
        Route::get('admin/librarian/create_modal', [\App\Http\Controllers\SchoolLibrarianController::class, 'create'])->name('admin.librarian.open_modal');
        Route::post('admin/librarian', [\App\Http\Controllers\SchoolLibrarianController::class, 'store'])->name('admin.librarian.create');
        Route::get('admin/librarian/{librarian}/edit', [\App\Http\Controllers\SchoolLibrarianController::class, 'edit'])->name('admin.librarian_edit_modal');
        Route::patch('admin/librarian/{librarian}', [\App\Http\Controllers\SchoolLibrarianController::class, 'update'])->name('admin.librarian.update');
        Route::delete('admin/librarian/{librarian}', [\App\Http\Controllers\SchoolLibrarianController::class, 'destroy'])->name('admin.librarian.delete');
        Route::patch('admin/librarian/{librarian}/status', [\App\Http\Controllers\SchoolLibrarianController::class, 'updateStatus'])->name('admin.librarian.status');
        Route::patch('admin/librarian/{librarian}/password', [\App\Http\Controllers\SchoolLibrarianController::class, 'updatePassword'])->name('admin.librarian.password');
        Route::get('admin/librarian/{librarian}/profile', [\App\Http\Controllers\SchoolLibrarianController::class, 'profile'])->name('admin.librarian.librarian_profile');
        Route::get('admin/librarian/{librarian}/documents', [\App\Http\Controllers\SchoolLibrarianController::class, 'documents'])->name('admin.librarian.documents');
        Route::post('admin/librarian/{librarian}/documents', [\App\Http\Controllers\SchoolLibrarianController::class, 'storeDocument'])->name('admin.librarian.documents.store');
        Route::delete('admin/librarian/{librarian}/documents/{document}', [\App\Http\Controllers\SchoolLibrarianController::class, 'destroyDocument'])->name('admin.librarian.documents.destroy');

        //Parent users route
        Route::get('admin/parent', [\App\Http\Controllers\SchoolParentController::class, 'index'])->name('admin.parent')->middleware('admin_permission');
        Route::post('admin/parent', [\App\Http\Controllers\SchoolParentController::class, 'store'])->name('admin.parent.create');
        Route::get('admin/parent/create', [\App\Http\Controllers\SchoolParentController::class, 'create'])->name('admin.parent.create_form');
        Route::get('admin/parent/classes/{class}/sections', [\App\Http\Controllers\SchoolParentController::class, 'sections'])->name('admin.parent.sections');
        Route::get('admin/parent/classes/{class}/sections/{section}/students', [\App\Http\Controllers\SchoolParentController::class, 'students'])->name('admin.parent.students');
        Route::get('admin/parent/{parent}/edit', [\App\Http\Controllers\SchoolParentController::class, 'edit'])->name('admin.parent_edit_modal');
        Route::patch('admin/parent/{parent}', [\App\Http\Controllers\SchoolParentController::class, 'update'])->name('admin.parent.update');
        Route::delete('admin/parent/{parent}', [\App\Http\Controllers\SchoolParentController::class, 'destroy'])->name('admin.parent.delete');
        Route::patch('admin/parent/{parent}/status', [\App\Http\Controllers\SchoolParentController::class, 'updateStatus'])->name('admin.parent.status');
        Route::patch('admin/parent/{parent}/password', [\App\Http\Controllers\SchoolParentController::class, 'updatePassword'])->name('admin.parent.password');
        Route::get('admin/parent/{parent}/profile', [\App\Http\Controllers\SchoolParentController::class, 'profile'])->name('admin.parent.parent_profile');
        Route::get('admin/parent/{parent}/documents', [\App\Http\Controllers\SchoolParentController::class, 'documents'])->name('admin.parent.documents');
        Route::post('admin/parent/{parent}/documents', [\App\Http\Controllers\SchoolParentController::class, 'storeDocument'])->name('admin.parent.documents.store');
        Route::delete('admin/parent/{parent}/documents/{document}', [\App\Http\Controllers\SchoolParentController::class, 'destroyDocument'])->name('admin.parent.documents.destroy');

        //Student users route
        Route::get('admin/student', 'studentList')->name('admin.student')->middleware('admin_permission');
        Route::get('admin/student/create_modal', 'createStudentModal')->name('admin.student.open_modal');
        Route::post('admin/student', 'studentCreate')->name('admin.student.create');
        Route::get('admin/student/id_card/{id}', 'studentIdCardGenerate')->name('admin.student.id_card');
        Route::get('admin/student/id_card_filter', 'studentIdCardFilter')->name('admin.student.id_card_filter')->middleware('admin_permission');
        Route::get('admin/student/edit/{id}', 'studentEditModal')->name('admin.student_edit_modal');
        Route::post('admin/student/{id}', 'studentUpdate')->name('admin.student.update');
        Route::get('admin/student/delete/{id}', 'studentDelete')->name('admin.student.delete');
        Route::get('admin/student/student_profile/{id}', 'studentProfile')->name('admin.student.student_profile');

        //Warden users route
        Route::get('admin/warden', [\App\Http\Controllers\SchoolWardenController::class, 'index'])->name('admin.warden')->middleware('admin_permission');
        Route::post('admin/warden', [\App\Http\Controllers\SchoolWardenController::class, 'store'])->name('admin.warden.create');
        Route::get('admin/warden/create', [\App\Http\Controllers\SchoolWardenController::class, 'create'])->name('admin.warden.create_form');
        Route::get('admin/warden/{warden}/edit', [\App\Http\Controllers\SchoolWardenController::class, 'edit'])->name('admin.warden_edit_modal');
        Route::patch('admin/warden/{warden}', [\App\Http\Controllers\SchoolWardenController::class, 'update'])->name('admin.warden.update');
        Route::delete('admin/warden/{warden}', [\App\Http\Controllers\SchoolWardenController::class, 'destroy'])->name('admin.warden.delete');
        Route::patch('admin/warden/{warden}/status', [\App\Http\Controllers\SchoolWardenController::class, 'updateStatus'])->name('admin.warden.status');
        Route::patch('admin/warden/{warden}/password', [\App\Http\Controllers\SchoolWardenController::class, 'updatePassword'])->name('admin.warden.password');
        Route::get('admin/warden/{warden}/profile', [\App\Http\Controllers\SchoolWardenController::class, 'profile'])->name('admin.warden.warden_profile');
        Route::get('admin/warden/{warden}/documents', [\App\Http\Controllers\SchoolWardenController::class, 'documents'])->name('admin.warden.documents');
        Route::post('admin/warden/{warden}/documents', [\App\Http\Controllers\SchoolWardenController::class, 'storeDocument'])->name('admin.warden.documents.store');
        Route::delete('admin/warden/{warden}/documents/{document}', [\App\Http\Controllers\SchoolWardenController::class, 'destroyDocument'])->name('admin.warden.documents.destroy');

        //User Account Status
        Route::get('admin/user_disable/{id}', 'account_disable')->name('admin.account_disable');
        Route::get('admin/user_enable/{id}', 'account_enable')->name('admin.account_enable');

        //Teacher permission route
        Route::get('admin/permission', [\App\Http\Controllers\SchoolTeacherPermissionController::class, 'index'])->name('admin.teacher.permission')->middleware('admin_permission');
        Route::get('admin/permission/classes/{class}/sections', [\App\Http\Controllers\SchoolTeacherPermissionController::class, 'sections'])->name('admin.teacher.permission.sections');
        Route::put('admin/permission', [\App\Http\Controllers\SchoolTeacherPermissionController::class, 'update'])->name('admin.teacher.permission.update');
        Route::put('admin/teacher/permission_update', static fn () => abort(410));

        //Admissions routes
        Route::get('admin/offline_admission/{type}', 'offlineAdmissionForm')->name('admin.offline_admission.single')->middleware('admin_permission')->middleware('admin_permission');
        Route::post('admin/offline_admission', 'offlineAdmissionCreate')->name('admin.offline_admission.create');
        Route::post('admin/offline_admission/bulk', 'offlineAdmissionBulkCreate')->name('admin.offline_admission.bulk_create');
        Route::post('admin/offline_admission/excel', 'offlineAdmissionExcelCreate')->name('admin.offline_admission.excel_create');
        Route::get('admin/offline_admission_preview', function () {
            return view('admin.offline_admission.csv_preview');
        })->name('admin.offline_admission.preview');

        //Exam category routes
        Route::get('admin/exam_category', 'examCategoryList')->name('admin.exam_category')->middleware('admin_permission');
        Route::get('admin/exam_category/create', 'createExamCategory')->name('admin.exam_category.open_modal');
        Route::post('admin/exam_category', 'examCategoryCreate')->name('admin.create.exam_category');
        Route::get('admin/exam_category/{id}', 'editExamCategory')->name('admin.edit.exam_category');
        Route::post('admin/exam_category/{id}', 'examCategoryUpdate')->name('admin.exam_category.update');
        Route::get('admin/exam_category/delete/{id}', 'examCategoryDelete')->name('admin.exam_category.delete');

        //Exam routes
        Route::get('admin/offline_exam', 'offlineExamList')->name('admin.offline_exam')->middleware('admin_permission');
        Route::get('admin/offline_exam/export/{id}', 'offlineExamExport')->name('admin.offline_exam.export');
        Route::get('admin/exam', 'createOfflineExam')->name('admin.offline_exam.open_modal');
        Route::post('admin/offline_exam', 'offlineExamCreate')->name('admin.create.offline_exam');
        Route::get('admin/offline_exam/{id}', 'editOfflineExam')->name('admin.edit.offline_exam');
        Route::post('admin/offline_exam/{id}', 'offlineExamUpdate')->name('admin.offline_exam.update');
        Route::get('admin/offline_exam/delete/{id}', 'offlineExamDelete')->name('admin.offline_exam.delete');
        Route::get('admin/exam_list_by_class/{id}', 'classWiseOfflineExam')->name('admin.class_wise_exam_list');

        //Admit Card
        Route::get('admin/admit-card-list', 'admitCardList')->name('admin.examination.admit_card_list');
        Route::get('admin/admit-card-create', 'admitCardCreate')->name('admin.examination.admit_card_create');
        Route::post('admin/admit-card-upload', 'admitCardUpload')->name('admin.examination.admit_card_upload');
        Route::get('admin/admit-card-edit/{id}', 'admitCardEdit')->name('admin.examination.admit_card_edit');
        Route::post('admin/admit-card-update/{id}', 'admitCardUpdate')->name('admin.examination.admit_card_update');
        Route::get('admin/admit-card-delete/{id}', 'admitCardDelete')->name('admin.examination.admit_card_delete');

        Route::get('admin/print-admit-card', 'admitCardPrint')->name('admin.examination.admit_card_print');
        Route::get('admin/admitCardFilter', 'admitCardFilter')->name('admin.examination.admitCardFilter');

        //Attendance routes
        Route::get('admin/attendance', [\App\Http\Controllers\SchoolAttendanceController::class, 'index'])->name('admin.daily_attendance')->middleware('admin_permission');
        Route::get('admin/attendance/export.pdf', [\App\Http\Controllers\SchoolAttendanceController::class, 'exportPdf'])->name('admin.attendance.export.pdf')->middleware('admin_permission');
        Route::put('admin/attendance', [\App\Http\Controllers\SchoolAttendanceController::class, 'update'])->name('admin.attendance.update');
        Route::get('admin/take_attendance', 'takeAttendance')->name('admin.take_attendance.open_modal');
        Route::post('admin/attendance_take', 'attendanceTake')->name('admin.attendance_take');
        Route::get('admin/attendance/student', 'studentListAttendance')->name('admin.attendance.student');
        Route::get('admin/attendance/filter', 'dailyAttendanceFilter')->name('admin.daily_attendance.filter');
        Route::get('admin/attendance/csv', 'dailyAttendanceFilter_csv')->name('admin.dailyAttendanceFilter_csv');

        //Routine routes
        Route::get('admin/routine', [\App\Http\Controllers\SchoolRoutineController::class, 'index'])->name('admin.routine')->middleware('admin_permission');
        Route::get('admin/routines/export.pdf', [\App\Http\Controllers\SchoolRoutineController::class, 'exportPdf'])->name('admin.routines.export.pdf')->middleware('admin_permission');
        Route::post('admin/routines', [\App\Http\Controllers\SchoolRoutineController::class, 'store'])->name('admin.routines.store');
        Route::patch('admin/routines/{routine}', [\App\Http\Controllers\SchoolRoutineController::class, 'update'])->name('admin.routines.update');
        Route::delete('admin/routines/{routine}', [\App\Http\Controllers\SchoolRoutineController::class, 'destroy'])->name('admin.routines.destroy');
        Route::get('admin/routine/add_routine', 'addRoutine')->name('admin.routine.open_modal');
        Route::post('admin/routine/routine_add', 'routineAdd')->name('admin.routine.routine_add');
        Route::get('admin/routine/list', 'routineList')->name('admin.routine.routine_list');
        Route::get('admin/routine/edit/{id}', 'routineEditModal')->name('admin.routine_edit_modal');
        Route::post('admin/routine/{id}', [\App\Http\Controllers\SchoolRoutineController::class, 'legacyEndpointGone'])->name('admin.routine.update');
        Route::get('admin/routine/delete/{id}', [\App\Http\Controllers\SchoolRoutineController::class, 'legacyEndpointGone'])->name('admin.routine.delete');

        //Syllabus routes
        Route::get('admin/syllabus', [\App\Http\Controllers\SchoolSyllabusController::class, 'index'])->name('admin.syllabus')->middleware('admin_permission');
        Route::get('admin/syllabuses/export.pdf', [\App\Http\Controllers\SchoolSyllabusController::class, 'exportPdf'])->name('admin.syllabuses.export.pdf')->middleware('admin_permission');
        Route::post('admin/syllabuses', [\App\Http\Controllers\SchoolSyllabusController::class, 'store'])->name('admin.syllabuses.store');
        Route::patch('admin/syllabuses/{syllabus}', [\App\Http\Controllers\SchoolSyllabusController::class, 'update'])->name('admin.syllabuses.update');
        Route::delete('admin/syllabuses/{syllabus}', [\App\Http\Controllers\SchoolSyllabusController::class, 'destroy'])->name('admin.syllabuses.destroy');
        Route::get('admin/syllabus/add_routine', 'addSyllabus')->name('admin.syllabus.open_modal');
        Route::post('admin/syllabus/routine_add', 'syllabusAdd')->name('admin.syllabus.syllabus_add');
        Route::get('admin/syllabus/list', 'syllabusList')->name('admin.syllabus.syllabus_list');
        Route::get('admin/syllabus/edit/{id}', 'syllabusEditModal')->name('admin.syllabus_edit_modal');
        Route::post('admin/syllabus/{id}', [\App\Http\Controllers\SchoolSyllabusController::class, 'legacyEndpointGone'])->name('admin.syllabus.update');
        Route::get('admin/syllabus/delete/{id}', [\App\Http\Controllers\SchoolSyllabusController::class, 'legacyEndpointGone'])->name('admin.syllabus.delete');

        //Gradebooks routes
        Route::get('admin/gradebook', [\App\Http\Controllers\SchoolGradebookController::class, 'index'])->name('admin.gradebook')->middleware('admin_permission');
        Route::get('admin/gradebook/export.pdf', [\App\Http\Controllers\SchoolGradebookController::class, 'exportPdf'])->name('admin.gradebook.export.pdf')->middleware('admin_permission');
        Route::post('admin/gradebook/results', [\App\Http\Controllers\SchoolGradebookController::class, 'store'])->name('admin.gradebook.results.store');
        Route::get('admin/gradebook/list', 'gradebookList')->name('admin.gradebook.list');
        Route::get('admin/gradebook/subjec_marks/{student_id}', 'subjectWiseMarks')->name('admin.gradebook.subject_wise_marks');
        Route::get('admin/exam/mark', 'addmark')->name('admin.exam_mark.open_modal');
        Route::post('admin/exam/mark_add', 'markAdd')->name('admin.add.exam_mark');

        //Marks route
        Route::get('admin/marks', 'marks')->name('admin.marks')->middleware('admin_permission');
        Route::get('admin/marks/list', 'marksFilter')->name('admin.marks.list');
        Route::get('admin/marks/list_pdf/{section_id?}/{class_id?}/{session_id?}/{exam_category_id?}/{subject_id?}', 'marksPdf')->name('admin.marks.list_pdf');

        //Grade routes
        Route::get('admin/grade', 'gradeList')->name('admin.grade_list')->middleware('admin_permission');
        Route::get('admin/grade_create', 'createGrade')->name('admin.grade.open_modal');
        Route::post('admin/grade', 'gradeCreate')->name('admin.create.grade');
        Route::get('admin/grade/{id}', 'editGrade')->name('admin.edit.grade');
        Route::post('admin/grade/{id}', 'gradeUpdate')->name('admin.grade.update');
        Route::get('admin/grade/delete/{id}', 'gradeDelete')->name('admin.grade.delete');

        //promotion routes
        Route::get('admin/promotion', 'promotionFilter')->name('admin.promotion')->middleware('admin_permission');
        Route::get('admin/promotion_list', 'promotionList')->name('admin.promotion.promotion_list');
        Route::get('admin/promote/{promotion_data}', 'promote')->name('admin.promotion.promote');

        //Subject routes
        Route::get('admin/subject', [\App\Http\Controllers\SchoolSubjectController::class, 'index'])->name('admin.subject_list')->middleware('admin_permission');
        Route::get('admin/subject/{subject}', [\App\Http\Controllers\SchoolSubjectController::class, 'legacyEndpointGone']);
        Route::post('admin/subject/delete/{subject}', [\App\Http\Controllers\SchoolSubjectController::class, 'legacyEndpointGone']);

        //Depertment routes
        Route::get('admin/department', 'departmentList')->name('admin.department_list')->middleware('admin_permission');
        Route::get('admin/department_create', 'createDepartment')->name('admin.department.open_modal');
        Route::post('admin/department', 'departmentCreate')->name('admin.create.department');
        Route::get('admin/department/{id}', 'editDepartment')->name('admin.edit.department');
        Route::post('admin/department/{id}', 'departmentUpdate')->name('admin.department.update');
        Route::get('admin/department/delete/{id}', 'departmentDelete')->name('admin.department.delete');

        //Class room routes
        Route::get('admin/class_room', [\App\Http\Controllers\SchoolClassRoomController::class, 'index'])->name('admin.class_room_list')->middleware('admin_permission');
        Route::get('admin/class_rooms/export.pdf', [\App\Http\Controllers\SchoolClassRoomController::class, 'exportPdf'])->name('admin.class_room.export.pdf')->middleware('admin_permission');
        Route::get('admin/class-rooms/create', [\App\Http\Controllers\SchoolClassRoomController::class, 'create'])->name('admin.class_room.open_modal');
        Route::post('admin/class-rooms', [\App\Http\Controllers\SchoolClassRoomController::class, 'store'])->name('admin.create.class_room');
        Route::get('admin/class-rooms/{classRoom}/edit', [\App\Http\Controllers\SchoolClassRoomController::class, 'edit'])->name('admin.edit.class_room');
        Route::patch('admin/class-rooms/{classRoom}', [\App\Http\Controllers\SchoolClassRoomController::class, 'update'])->name('admin.class_room.update');
        Route::delete('admin/class-rooms/{classRoom}', [\App\Http\Controllers\SchoolClassRoomController::class, 'destroy'])->name('admin.class_room.delete');
        Route::patch('admin/class_room/{classRoom}', [\App\Http\Controllers\SchoolClassRoomController::class, 'legacyEndpointGone']);
        Route::delete('admin/class_room/delete/{classRoom}', [\App\Http\Controllers\SchoolClassRoomController::class, 'legacyEndpointGone']);

        //Class list routes
        Route::get('admin/class_list', [\App\Http\Controllers\SchoolClassController::class, 'index'])->name('admin.class_list')->middleware('admin_permission');
        Route::get('admin/classes/export.pdf', [\App\Http\Controllers\SchoolClassController::class, 'exportPdf'])->name('admin.class.export.pdf')->middleware('admin_permission');
        Route::get('admin/classes/create', [\App\Http\Controllers\SchoolClassController::class, 'create'])->name('admin.class.open_modal');
        Route::post('admin/classes', [\App\Http\Controllers\SchoolClassController::class, 'store'])->name('admin.create.class');
        Route::get('admin/classes/{class}/edit', [\App\Http\Controllers\SchoolClassController::class, 'edit'])->name('admin.edit.class');
        Route::patch('admin/classes/{class}', [\App\Http\Controllers\SchoolClassController::class, 'update'])->name('admin.class.update');
        Route::delete('admin/classes/{class}', [\App\Http\Controllers\SchoolClassController::class, 'destroy'])->name('admin.class.delete');
        Route::patch('admin/class/{class}', [\App\Http\Controllers\SchoolClassController::class, 'legacyEndpointGone']);
        Route::patch('admin/class/sections/{class}', [\App\Http\Controllers\SchoolClassController::class, 'legacyEndpointGone']);
        Route::delete('admin/class/delete/{class}', [\App\Http\Controllers\SchoolClassController::class, 'legacyEndpointGone']);

        //Accounting route
        Route::get('admin/student_fee/delete/{id}/{status}', 'update_offline_payment')->name('admin.update_offline_payment');

        //Student fee manager routes
        Route::get('admin/fee_manager', 'studentFeeManagerList')->name('admin.fee_manager.list')->middleware('admin_permission');
        Route::get('admin/student_fee_manager/export/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExport')->name('admin.fee_manager.export');
        Route::get('admin/student_fee_manager/pdf_print/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExportPdfPrint')->name('admin.fee_manager.pdf_print');
        Route::get('admin/fee_manager_create/{value}', 'createFeeManager')->name('admin.fee_manager.open_modal');
        Route::post('admin/fee_manager/{value}', 'feeManagerCreate')->name('admin.create.fee_manager');
        Route::get('admin/fee_manager/{id}', 'editFeeManager')->name('admin.edit.fee_manager');
        Route::post('admin/fee_manager_list/{id}', 'feeManagerUpdate')->name('admin.fee_manager.update');
        Route::get('admin/student_fee/delete/{id}', 'studentFeeDelete')->name('admin.fee_manager.delete');
        Route::get('admin/student_fee/invoice/{id}', 'studentFeeinvoice')->name('admin.studentFeeinvoice');
        Route::get('admin/offline_payment/pending', 'offline_payment_pending')->name('admin.offline_payment_pending')->middleware('admin_permission');

        //Expense routes
        Route::get('admin/expenses/list', 'expenseList')->name('admin.expense.list')->middleware('admin_permission');
        Route::get('admin/expenses/create', 'createExpense')->name('admin.expenses.open_modal');
        Route::post('admin/expenses/added', 'expenseCreate')->name('admin.create.expenses');
        Route::get('admin/expenses/{id}', 'editExpense')->name('admin.edit.expenses');
        Route::post('admin/expenses/{id}', 'expenseUpdate')->name('admin.expenses.update');
        Route::get('admin/expenses/delete/{id}', 'expenseDelete')->name('admin.expense.delete');

        //Expense category routes
        Route::get('admin/expense_category/list', 'expenseCategoryList')->name('admin.expense.category_list')->middleware('admin_permission');
        Route::get('admin/expense_category/create', 'createExpenseCategory')->name('admin.expense_category.open_modal');
        Route::post('admin/expense_category/added', 'expenseCategoryCreate')->name('admin.create.expense_category');
        Route::get('admin/expense_category/{id}', 'editExpenseCategory')->name('admin.edit.expense_category');
        Route::post('admin/expense_category/{id}', 'expenseCategoryUpdate')->name('admin.expense_category.update');
        Route::get('admin/expense_category/delete/{id}', 'expenseCategoryDelete')->name('admin.expense.category_delete');

        //Book routes
        Route::get('admin/book/list', 'bookList')->name('admin.book.book_list')->middleware('admin_permission');
        Route::get('admin/book/create', 'createBook')->name('admin.book.open_modal');
        Route::post('admin/book/added', 'bookCreate')->name('admin.create.book');
        Route::get('admin/book/{id}', 'editBook')->name('admin.edit.book');
        Route::post('admin/book/{id}', 'bookUpdate')->name('admin.book.update');
        Route::get('admin/book/delete/{id}', 'bookDelete')->name('admin.book.delete');

        //Issue book routes
        Route::get('admin/book_issue', 'bookIssueList')->name('admin.book_issue.list')->middleware('admin_permission');
        Route::get('admin/book_issue/create', 'createBookIssue')->name('admin.book_issue.open_modal');
        Route::post('admin/book_issue/added', 'bookIssueCreate')->name('admin.create.book_issue');
        Route::get('admin/book_issue/{id}', 'editBookIssue')->name('admin.edit.book_issue');
        Route::post('admin/book_issue/{id}', 'bookIssueUpdate')->name('admin.book_issue.update');
        Route::get('admin/book_issue/return/{id}', 'bookIssueReturn')->name('admin.book_issue.return');
        Route::get('admin/book_issue/delete/{id}', 'bookIssueDelete')->name('admin.book_issue.delete');

        //Noticeboard routes
        Route::get('admin/noticeboard', 'noticeboardList')->name('admin.noticeboard.list')->middleware('admin_permission');
        Route::get('admin/noticeboard/create', 'createNoticeboard')->name('admin.noticeboard.open_modal');
        Route::post('admin/noticeboard/added', 'noticeboardCreate')->name('admin.create.noticeboard');
        Route::get('admin/noticeboard/{id}', 'editNoticeboard')->name('admin.edit.noticeboard');
        Route::post('admin/noticeboard/{id}', 'noticeboardUpdate')->name('admin.noticeboard.update');
        Route::get('admin/noticeboard/delete/{id}', 'noticeboardDelete')->name('admin.noticeboard.delete');

        //Event routes
        Route::get('admin/events/list', 'eventList')->name('admin.events.list')->middleware('admin_permission');
        Route::get('admin/events/create', 'createEvent')->name('admin.events.open_modal');
        Route::post('admin/events/added', 'eventCreate')->name('admin.create.event');
        Route::get('admin/events/{id}', 'editEvent')->name('admin.edit.event');
        Route::post('admin/events/{id}', 'eventUpdate')->name('admin.event.update');
        Route::get('admin/events/delete/{id}', 'eventDelete')->name('admin.events.delete');


        // Club Management
        Route::get('admin/club/list', 'clubList')->name('admin.club.index')->middleware('admin_permission');
        Route::get('admin/club/create', 'createClub')->name('admin.club.create');
        Route::post('admin/club/store', 'store')->name('admin.club.store');
        Route::post('admin/club/toggle-status/{id}', 'toggleStatus')->name('admin.club.toggle_status');
        Route::get('admin/club/edit/{id}', 'editClub')->name('admin.club.edit');
        Route::post('admin/club/update/{id}', 'updateClub')->name('admin.club.update');
        Route::get('admin/club/delete/{id}', 'deleteClub')->name('admin.club.delete');
        Route::get('admin/club/members/{club}', 'clubMembers')->name('admin.club.members');
        Route::get('admin/club/member/approve/{id}', 'approveMember')->name('admin.club.member.approve');
        Route::get('admin/club/member/disable/{id}', 'member_disable')->name('admin.club.member.disable');
        Route::post('admin/club/member/reject/{id}', 'rejectMember')->name('admin.club.member.reject');
        Route::get('admin/club/member/delete/{id}', 'deleteMember')->name('admin.club.member.delete');
        Route::get('admin/club/member/add/{club}', 'addMemberForm')->name('admin.club.add_member');
        Route::post('admin/club/member/store', 'storeMember')->name('admin.club.member.store');
        Route::get('admin/club/{club}/members/search', 'searchMembers')->name('admin.club.members.search');
        Route::get('admin/club/students/search', 'searchStudents')->name('admin.club.students.search');


        // Club Notice
        Route::get('admin/club/notice/{club}', 'notice1_index')->name('admin.club.notice');
        Route::get('admin/club/notice/create/{club}', 'notice_create')->name('admin.club.notice.create');
        Route::post('admin/club/notice/store', 'notice_store')->name('admin.club.notice.store');
        Route::get('admin/club/notice/edit/{id}', 'notice_edit')->name('admin.club.notice.edit');
        Route::post('admin/club/notice/update/{id}', 'notice_update')->name('admin.club.notice.update');
        Route::get('admin/club/notice/delete/{id}', 'notice_delete')->name('admin.club.notice.delete');








        //Complain List routes
        Route::get('admin/complain/complainList', 'complainList')->name('admin.complain.complainList');

        //Settings routes
        Route::get('admin/settings/payment', 'paymentSettings')->name('admin.settings.payment')->middleware('admin_permission');
        Route::post('admin/settings/payment/post', 'paymentSettings_post')->name('admin.settings.payment_post');
        Route::get('admin/settings/school', 'schoolSettings')->name('admin.settings.school')->middleware('admin_permission');
        Route::post('admin/settings/school', 'schoolUpdate')->name('admin.school.update');

        //Session routes
        Route::get('admin/session_manager', 'sessionManager')->name('admin.settings.session_manager')->middleware('admin_permission');
        Route::get('admin/session_manager/active_session/{id}', 'activeSession')->name('admin.session_manager.active_session');
        Route::get('admin/session_manager/create', 'createSession')->name('admin.create.session');
        Route::post('admin/session_add', 'sessionCreate')->name('admin.session_manager.create');
        Route::get('admin/session_manager/{id}', 'editSession')->name('admin.edit.session');
        Route::post('admin/session_manager/{id}', 'sessionUpdate')->name('admin.session.update');
        Route::get('admin/session_manager/delete/{id}', 'sessionDelete')->name('admin.session.delete');

        //Profile
        Route::get('admin/profile', 'profile')->name('admin.profile')->middleware('admin_permission');
        Route::post('admin/profile/update', 'profile_update')->name('admin.profile.update');
        Route::any('admin/password/{action_type}', 'password')->name('admin.password');
        Route::post('admin/language', 'user_language')->name('admin.language');

        //Account disable navigation
        //Route::get('admin/account_disableview', 'account_disableview')->name('admin.account_disableview');

        // Student Feedback
        Route::get('admin/feedback-list', 'feedback_list')->name('admin.feedback.feedback_list');
        Route::get('admin/feedback-create', 'create_feedback')->name('admin.feedback.create_feedback');
        Route::post('admin/feedback-upload', 'upload_feedback')->name('admin.feedback.upload_feedback');
        Route::get('admin/feedback-edit/{id}', 'edit_feedback')->name('admin.feedback.edit_feedback');
        Route::post('admin/feedback-update/{id}', 'update_feedback')->name('admin.feedback.update_feedback');
        Route::get('admin/feedback-delete/{id}', 'delete_feedback')->name('admin.feedback.delete_feedback');

        // Hostel
        Route::get('admin/hostel-list', 'hostel_list')->name('admin.hostel.hostel_list');
        Route::get('admin/hostel-create', 'create_hostel')->name('admin.hostel.create_hostel');
        Route::post('admin/hostel-store', 'store_hostel')->name('admin.hostel.store_hostel');
        Route::get('admin/hostel-edit/{id}', 'edit_hostel')->name('admin.hostel.edit_hostel');
        Route::post('admin/hostel-update/{id}', 'update_hostel')->name('admin.hostel.update_hostel');
        Route::get('admin/hostel-delete/{id}', 'delete_hostel')->name('admin.hostel.delete_hostel');

        // Hostel Room
        Route::get('admin/hostel-room-list', 'hostel_room_list')->name('admin.hostel.room_list');
        Route::get('admin/hostel-room-create', 'create_hostel_room')->name('admin.hostel.create_room');
        Route::post('admin/hostel-room-store', 'store_hostel_room')->name('admin.hostel.store_room');
        Route::get('admin/hostel-room-edit/{id}', 'edit_hostel_room')->name('admin.hostel.edit_room');
        Route::post('admin/hostel-room-update/{id}', 'update_hostel_room')->name('admin.hostel.update_room');
        Route::get('admin/hostel-room-delete/{id}', 'delete_hostel_room')->name('admin.hostel.delete_room');

        // Hostel Room Allocation
        Route::get('admin/hostel-room-allocation-list', 'hostel_room_allocation_list')->name('admin.hostel.allocation_list');
        Route::get('admin/hostel-room-allocation-create', 'create_hostel_room_allocation')->name('admin.hostel.create_allocation');
        Route::post('admin/hostel-room-allocation-store', 'store_hostel_room_allocation')->name('admin.hostel.store_allocation');
        Route::get('admin/hostel-room-allocation-edit/{id}', 'edit_hostel_room_allocation')->name('admin.hostel.edit_allocation');
        Route::post('admin/hostel-room-allocation-update/{id}', 'update_hostel_room_allocation')->name('admin.hostel.update_allocation');
        Route::get('admin/hostel-room-allocation-delete/{id}', 'delete_hostel_room_allocation')->name('admin.hostel.delete_allocation');
        Route::get('admin/hostel-applications', 'applications')->name('admin.hostel.applications');
        Route::get('admin/hostel-applications/approve/{id}', 'approveApplication')->name('admin.hostel.applications.approve');
        Route::get('admin/hostel-applications/reject/{id}', 'rejectApplication')->name('admin.hostel.applications.reject');

        Route::get('admin/hostel-fee-manager', 'hostelFees')->name('admin.hostel_fee_manager.list');

        //Appraisal
        Route::get('admin/appraisal-question', 'appraisalQuestions')->name('admin.appraisal.appraisalQuestions');
        Route::get('admin/appraisal-create-question', 'createQuestion')->name('admin.appraisal.createQuestion');
        Route::post('admin/appraisal-store-question', 'storeQuestion')->name('admin.appraisal.storeQuestion');
        Route::get('admin/appraisal-edit/{id}', 'appraisalQuestionEdit')->name('admin.appraisal.appraisalQuestionEdit');
        Route::post('admin/appraisal-question-update/{id}', 'appraisalQuestionUpdate')->name('admin.appraisal.appraisalQuestionUpdate');
        Route::get('admin/appraisal-question-delete/{id}', 'appraisalQuestionDelete')->name('admin.appraisal.appraisalQuestionDelete');
        Route::get('admin/appraisal-student-feedback', 'appraisalFeedback')->name('admin.appraisal.studentFeedback');

        // Message
        Route::get('admin/message/all-message/{id}', 'allMessage')->name('admin.message.all_message');
        Route::get('admin/message/message-thrades/{id}', 'messagethrades')->name('admin.message.messagethrades');
        Route::post('admin/message/single-chat/save', 'chat_save')->name('admin.message.chat_save');
        Route::get('admin/message/chat_empty', 'chat_empty')->name('admin.message.chat_empty');

        // hostel
        Route::get('admin/hostel-fee/offline-payment/list', 'offlinePaymentList')->name('admin.offline.payment.hostel.list');

        Route::get('admin/hostel_fee/offline_payment/accept/{id}', 'acceptOfflinePaymentHostel')->name('admin.accept.offline.payment.hostel');
        Route::get('admin/hostel_fee/offline_payment/reject/{id}', 'rejectOfflinePaymentHostel')->name('admin.reject.offline.payment.hostel');

        // Canteen
        Route::get('admin/canteen-dashboard', 'canteen_dashboard')->name('admin.canteen.dashboard');

        Route::get('admin/canteen-category-list', 'canteen_category_list')->name('admin.canteen.category_list');
        Route::get('admin/canteen-category-create', 'canteen_category_create')->name('admin.canteen.category_create');
        Route::post('admin/canteen-category-store', 'canteen_category_store')->name('admin.canteen.category_store');
        Route::get('admin/canteen-category-edit/{id}', 'canteen_category_edit')->name('admin.canteen.category_edit');
        Route::post('admin/canteen-category-update/{id}', 'canteen_category_update')->name('admin.canteen.category_update');
        Route::get('admin/canteen-category-delete/{id}', 'canteen_category_delete')->name('admin.canteen.category_delete');

        Route::get('admin/canteen-item-list', 'canteen_item_list')->name('admin.canteen.item_list');
        Route::get('admin/canteen-item-create', 'canteen_item_create')->name('admin.canteen.item_create');
        Route::post('admin/canteen-item-store', 'canteen_item_store')->name('admin.canteen.item_store');
        Route::get('admin/canteen-item-edit/{id}', 'canteen_item_edit')->name('admin.canteen.item_edit');
        Route::post('admin/canteen-item-update/{id}', 'canteen_item_update')->name('admin.canteen.item_update');
        Route::get('admin/canteen-item-delete/{id}', 'canteen_item_delete')->name('admin.canteen.item_delete');

        Route::get('admin/canteen-pos', 'canteen_pos')->name('admin.canteen.pos');
        Route::post('admin/canteen-pos-store', 'canteen_pos_store')->name('admin.canteen.pos_store');

        Route::get('admin/canteen-order-list', 'canteen_order_list')->name('admin.canteen.order_list');
        Route::get('admin/canteen-order-detail/{id}', 'canteen_order_detail')->name('admin.canteen.order_detail');
        Route::post('admin/canteen-order-status/{id}', 'canteen_order_status_update')->name('admin.canteen.order_status');
        Route::get('admin/canteen-order-delete/{id}', 'canteen_order_delete')->name('admin.canteen.order_delete');

        Route::get('admin/canteen-report-daily', 'canteen_report_daily')->name('admin.canteen.report_daily');
        Route::get('admin/canteen-report-monthly', 'canteen_report_monthly')->name('admin.canteen.report_monthly');
        Route::get('admin/canteen-report-top-selling', 'canteen_report_top_selling')->name('admin.canteen.report_top_selling');
        Route::get('admin/canteen-report-student-history', 'canteen_report_student_history')->name('admin.canteen.report_student_history');
    });
    //Admin routes end here

    //Teacher routes are here
    Route::controller(TeacherController::class)->middleware('teacher', 'auth')->group(function () {

        Route::get('teacher/dashboard', 'teacherDashboard')->name('teacher.dashboard')->middleware('role_id');

        //Attendance routes
        Route::get('teacher/attendance', 'dailyAttendance')->name('teacher.daily_attendance');
        Route::get('teacher/take_attendance', 'takeAttendance')->name('teacher.take_attendance.open_modal');
        Route::post('teacher/attendance_take', 'attendanceTake')->name('teacher.attendance_take');
        Route::get('teacher/attendance/student', 'studentListAttendance')->name('teacher.attendance.student');
        Route::get('teacher/attendance/filter', 'dailyAttendanceFilter')->name('teacher.daily_attendance.filter');
        Route::get('teacher/attendance/csv', 'dailyAttendanceFilter_csv')->name('teacher.dailyAttendanceFilter_csv');

        //Marks routes
        Route::get('teacher/marks', 'marks')->name('teacher.marks');
        Route::get('teacher/marks/list', 'marksFilter')->name('teacher.marks.list');

        //Offline exam routes
        Route::get('teacher/offline_exam', 'offlineExamList')->name('teacher.offline_exam');
        Route::get('teacher/offline_exam/export/{id}', 'offlineExamExport')->name('teacher.offline_exam.export');
        Route::get('teacher/exam_list_by_class/{id}', 'classWiseOfflineExam')->name('teacher.class_wise_exam_list');

        //Routine routes
        Route::get('teacher/routine', 'routine')->name('teacher.routine');
        Route::get('teacher/routine/list', 'routineList')->name('teacher.routine.routine_list');

        //Subject routes
        Route::get('teacher/subject', 'subjectList')->name('teacher.subject_list');

        //Gradebook routes
        Route::get('teacher/gradebook', 'gradebook')->name('teacher.gradebook');
        Route::get('teacher/gradebook/list', 'gradebookList')->name('teacher.gradebook.list');
        Route::get('teacher/gradebook/subjec_marks/{student_id}', 'subjectWiseMarks')->name('teacher.gradebook.subject_wise_marks');

        //Syllabus routes
        Route::get('teacher/syllabus', 'list_of_syllabus')->name('teacher.list_of_syllabus');
        Route::get('teacher/class_wise_section_for_syllabus', 'class_wise_section_for_syllabus')->name('teacher.class_wise_section_for_syllabus');
        Route::get('teacher/syllabus_details', 'syllabus_details')->name('teacher.syllabus_details');
        Route::get('teacher/create/syllabus/modal', 'show_syllabus_modal')->name('teacher.show_syllabus_modal');
        Route::post('teacher/create/syllabus/modal/post', 'show_syllabus_modal_post')->name('teacher.show_syllabus_modal_post');
        Route::get('teacher/syllabus/delete/{id}', 'syllabusDelete')->name('teacher.syllabus.delete');

        //Noticeboard routes
        Route::get('teacher/noticeboard', 'noticeboardList')->name('teacher.noticeboard.list');
        Route::get('teacher/noticeboard/{id}', 'editNoticeboard')->name('teacher.edit.noticeboard');

        //Event routes
        Route::get('teacher/events/list', 'eventList')->name('teacher.events.list');

        // Club Management
        Route::get('teacher/club/list', 'club')->name('teacher.club.list');
        Route::get('teacher/club/create', 'createClub')->name('teacher.club.create');
        Route::post('teacher/club/store', 'store')->name('teacher.club.store');
        Route::post('teacher/club/toggle-status/{id}', 'toggleStatus')->name('teacher.club.toggle_status');
        Route::get('teacher/club/edit/{id}', 'editClub')->name('teacher.club.edit');
        Route::post('teacher/club/update/{id}', 'updateClub')->name('teacher.club.update');
        Route::get('teacher/club/delete/{id}', 'deleteClub')->name('teacher.club.delete');
        Route::get('teacher/club/members/{club}', 'clubMembers')->name('teacher.club.members');
        Route::get('teacher/club/member/approve/{id}', 'approveMember')->name('teacher.club.member.approve');
        Route::get('teacher/club/member/disable/{id}', 'member_disable')->name('teacher.club.member.disable');
        Route::post('teacher/club/member/reject/{id}', 'rejectMember')->name('teacher.club.member.reject');
        Route::get('teacher/club/member/delete/{id}', 'deleteMember')->name('teacher.club.member.delete');
        Route::get('teacher/club/member/add/{club}', 'addMemberForm')->name('teacher.club.add_member');
        Route::post('teacher/club/member/store', 'storeMember')->name('teacher.club.member.store');
        Route::get('teacher/club/{club}/members/search', 'searchMembers')->name('teacher.club.members.search');
        Route::get('teacher/club/students/search', 'searchStudents')->name('teacher.club.students.search');

        // club Notice
        Route::get('teacher/club/notice/{club}', 'notice1_index')->name('teacher.club.notice');
        Route::get('teacher/club/{club}/notice/create', 'notice_create')->name('teacher.club.notice.create');
        Route::post('teacher/club/notice/store', 'notice_store')->name('teacher.club.notice.store');
        Route::get('teacher/club/notice/edit/{notice}', 'notice_edit')->name('teacher.club.notice.edit');
        Route::post('teacher/club/notice/update/{notice}', 'notice_update')->name('teacher.club.notice.update');
        Route::get('teacher/club/notice/delete/{notice}', 'notice_delete')->name('teacher.club.notice.delete');


        //Profile
        Route::get('teacher/profile', 'profile')->name('teacher.profile');
        Route::post('teacher/profile/update', 'profile_update')->name('teacher.profile.update');
        Route::any('teacher/password/{action_type}', 'password')->name('teacher.password');
        Route::post('teacher/language', 'user_language')->name('teacher.language');

        // Student Feedback
        Route::get('teacher/feedback-list', 'feedback_list')->name('teacher.feedback.feedback_list');
        Route::get('teacher/feedback-create', 'create_feedback')->name('teacher.feedback.create_feedback');
        Route::post('teacher/feedback-upload', 'upload_feedback')->name('teacher.feedback.upload_feedback');
        Route::get('teacher/feedback-edit/{id}', 'edit_feedback')->name('teacher.feedback.edit_feedback');
        Route::post('teacher/feedback-update/{id}', 'update_feedback')->name('teacher.feedback.update_feedback');
        Route::get('teacher/feedback-delete/{id}', 'delete_feedback')->name('teacher.feedback.delete_feedback');

        // Message
        Route::get('teacher/message/all-message/{id}', 'allMessage')->name('teacher.message.all_message');
        Route::get('teacher/message/message-thrades/{id}', 'messagethrades')->name('teacher.message.messagethrades');
        Route::post('teacher/message/single-chat/save', 'chat_save')->name('teacher.message.chat_save');
        Route::get('teacher/message/chat_empty', 'chat_empty')->name('teacher.message.chat_empty');
    });
    //Teacher routes end here

    //Parent routes are here
    Route::controller(ParentController::class)->middleware('parent', 'auth')->group(function () {

        Route::get('parent/dashboard', 'parentDashboard')->name('parent.dashboard')->middleware('role_id');

        //User routes
        Route::get('parent/teacherlist', 'teacherList')->name('parent.teacherlist');
        Route::get('parent/childlist', 'childList')->name('parent.childlist');
        Route::get('parent/student/id_card/{id}', 'studentIdCardGenerate')->name('parent.student.id_card');

        //Fee manager routes
        Route::get('parent/fee_manager', 'FeeManagerList')->name('parent.fee_manager.list');
        Route::get('parent/fee_manager/payment/{id}', 'FeePayment')->name('parent.FeePayment');
        Route::get('parent/fee_manager/export/{date_from}/{date_to}/{selected_status}', 'feeManagerExport')->name('parent.fee_manager.export');
        Route::get('parent/student_fee/invoice/{id}', 'studentFeeinvoice')->name('parent.studentFeeinvoice');

        //Grade rotues
        Route::get('parent/grade', 'gradeList')->name('parent.grade_list');

        //Subject routes
        Route::get('parent/child/subjects', 'subjectList')->name('parent.subject_list');
        Route::get('parent/child/subject/list', 'subjectList_by_student_name')->name('parent.subjectList_by_student_name');

        //Syllabus routes
        Route::get('parent/child/syllabus', 'syllabusList')->name('parent.syllabus_list');
        Route::get('parent/child/syllabus/list', 'syllabusList_by_student_name')->name('parent.syllabusList_by_student_name');

        //Online payment routes
        Route::get('parent/payment/success/{user_data}/{response}', 'student_fee_success_payment')->name('parent.student_fee_success_payment');
        Route::get('parent/payment/fail/{user_data}/{response}', 'student_fee_fail_payment')->name('parent.student_fee_fail_payment');

        //Offline payment routes
        Route::post('parent/student_fee/offline_payment/{id}', 'offlinePayment')->name('parent.offline_payment');

        //Routine routes
        Route::get('parent/routine', 'routine')->name('parent.routine');
        Route::get('parent/routine/list', 'routineList')->name('parent.routine.routine_list');

        //Attendence routes
        Route::get('parent/attendence/list', 'list_of_attendence')->name('parent.list_of_attendence');
        Route::get('parent/attendance/filter', 'list_of_attendence')->name('parent.daily_attendance.filter');
        Route::get('parent/attendance/csv', 'dailyAttendanceFilter_csv')->name('parent.dailyAttendanceFilter_csv');

        //Marks routes
        Route::get('parent/marks', 'marks')->name('parent.marks');
        Route::get('parent/marks/list', 'marks_list')->name('parent.marks_list');

        //Noticeboard routes
        Route::get('parent/noticeboard', 'noticeboardList')->name('parent.noticeboard.list');
        Route::get('parent/noticeboard/{id}', 'editNoticeboard')->name('parent.edit.noticeboard');

        //Event routes
        Route::get('parent/events/list', 'eventList')->name('parent.events.list');

        //Profile
        Route::get('parent/profile', 'profile')->name('parent.profile');
        Route::post('parent/profile/update', 'profile_update')->name('parent.profile.update');
        Route::any('parent/password/{action_type}', 'password')->name('parent.password');
        Route::post('parent/language', 'user_language')->name('parent.language');

        // Student Feedback
        Route::get('parent/feedback/filter', 'filter')->name('parent.feedback.filter');
        Route::get('parent/feedback-list', 'feedback_list')->name('parent.feedback.feedback_list');

        // Message
        Route::get('parent/message/all-message/{id}', 'allMessage')->name('parent.message.all_message');
        Route::get('parent/message/message-thrades/{id}', 'messagethrades')->name('parent.message.messagethrades');
        Route::post('parent/message/single-chat/save', 'chat_save')->name('parent.message.chat_save');
        Route::get('parent/message/chat_empty', 'chat_empty')->name('parent.message.chat_empty');
    });
    //Parent routes end here

    //Student routes are here
    Route::controller(StudentController::class)->middleware('student', 'auth')->group(function () {

        Route::get('student/dashboard', 'studentDashboard')->name('student.dashboard')->middleware('role_id');

        //User routes
        Route::get('student/teacher', 'teacherList')->name('student.teacher');

        //Attendance routes
        Route::get('student/attendance', 'dailyAttendance')->name('student.daily_attendance');
        Route::get('student/attendance/filter', 'dailyAttendance')->name('student.daily_attendance.filter');
        Route::get('student/attendance/csv', 'dailyAttendanceFilter_csv')->name('student.dailyAttendanceFilter_csv');

        //Routine routes
        Route::get('student/routine', 'routine')->name('student.routine');

        //Subject routes
        Route::get('student/subject', 'subjectList')->name('student.subject_list');

        //Syllabus routes
        Route::get('student/syllabus', 'syllabus')->name('student.syllabus');

        //Grade routes
        Route::get('student/grade', 'gradeList')->name('student.grade_list');

        //Book routes
        Route::get('student/book/list', 'bookList')->name('student.book.book_list');
        Route::get('student/book_issue', 'bookIssueList')->name('student.book.issued_list');

        //Marks routes
        Route::get('student/marks', 'marks')->name('student.marks');

        //Noticeboard routes
        Route::get('student/noticeboard', 'noticeboardList')->name('student.noticeboard.list');
        Route::get('student/noticeboard/{id}', 'editNoticeboard')->name('student.edit.noticeboard');

        //Event routes
        Route::get('student/events/list', 'eventList')->name('student.events.list');

        //Complain routes
        Route::get('student/complain/complain', 'complain')->name('student.complain.complain');
        Route::get('student/complain/complainUser', 'complainUser')->name('student.complain.complainUser');

        //Fee manager routes
        Route::get('student/fee_manager', 'FeeManagerList')->name('student.fee_manager.list');
        Route::get('student/fee_manager/payment/{id}', 'FeePayment')->name('student.FeePayment');
        Route::get('student/fee_manager/export/{date_from}/{date_to}/{selected_status}', 'feeManagerExport')->name('student.fee_manager.export');
        Route::get('student/payment/success/{user_data}/{response}', 'student_fee_success_payment_student')->name('student.student_fee_success_payment_student');
        Route::get('student/payment/fail/{user_data}/{response}', 'student_fee_fail_payment_student')->name('student.student_fee_fail_payment_student');
        Route::post('student/student_fee/offline_payment/{id}', 'offlinePaymentStudent')->name('student.offline_payment');
        Route::get('student/student_fee/invoice/{id}', 'studentFeeinvoice')->name('student.studentFeeinvoice');

        //Profile
        Route::get('student/profile', 'profile')->name('student.profile');
        Route::post('student/profile/update', 'profile_update')->name('student.profile.update');
        Route::any('student/password/{action_type}', 'password')->name('student.password');
        Route::post('student/language', 'user_language')->name('student.language');

        //Appraisal
        Route::get('student/appraisal-list', 'appraisalList')->name('student.appraisal.appraisalList');
        Route::get('student/start-appraisal/{id}', 'singleAppraisal')->name('student.appraisal.singleAppraisal');
        Route::post('student/submit-appraisal/{id}', 'appraisalSubmit')->name('student.appraisal.submit');

        // Message
        Route::get('student/message/all-message/{id}', 'allMessage')->name('student.message.all_message');
        Route::get('student/message/message-thrades/{id}', 'messagethrades')->name('student.message.messagethrades');
        Route::post('student/message/single-chat/save', 'chat_save')->name('student.message.chat_save');
        Route::get('student/message/chat_empty', 'chat_empty')->name('student.message.chat_empty');

        // Hostel
        Route::get('student/hostel-applications', 'hostelApplications')->name('student.hostel.applications');
        Route::get('student/hostel-applications/create', 'applicationCreate')->name('student.hostel.applications.create');
        Route::post('student/hostel-applications/store', 'applicationStore')->name('student.hostel.applications.store');
        Route::get('student/hostel-applications/{id}/edit', 'applicationEdit')->name('student.hostel.applications.edit');
        Route::post('student/hostel-applications/{id}/update', 'applicationUpdate')->name('student.hostel.applications.update');
        Route::get('student/hostel-applications/{id}/delete', 'applicationDelete')->name('student.hostel.applications.delete');

        // Hostel Fee manager routes
        Route::get('student/hostel_fee_manager', 'hostelFeeManagerList')->name('student.hostel_fee_manager.list');
        Route::get('student/hostel_fee_payment', 'hostelFeePayment')->name('student.hostel_fee.payment');
        Route::get('student/hostel_fee_manager/export/{date_from}/{date_to}/{selected_status}', 'hostelFeeManagerExport')->name('student.hostel_fee_manager.export');
        Route::get('student/hostel_payment/success/{user_data}/{response}', 'student_hostel_fee_success_payment_student')->name('student.student_hostel_fee_success_payment_student');
        Route::get('student/hostel_payment/fail/{user_data}/{response}', 'student_hostel_fee_fail_payment_student')->name('student.student_hostel_fee_fail_payment_student');
        Route::post('student/hostel_fee/offline_payment', 'offlinePaymentHostel')->name('student.offline.payment.hostel');
        Route::get('student/hostel_fee/invoice/{id}', 'hostelFeeInvoice')->name('student.hostel_fee.invoice');
        Route::get('student/hostel_fee/pay/{month}/{year}', 'payMonthlyFee')->name('student.hostel_fee.pay_monthly');

        // Club Management
        Route::get('student/club/list', 'club')->name('student.club.list');
        Route::post('student/club/toggle-status/{id}', 'toggleStatus')->name('student.club.toggle_status');
        Route::get('student/club/join/{club}', 'join')->name('club.join');
        Route::get('student/club/remove-request/{club}', 'removeRequest')->name('club.removeRequest');
        Route::get('student/club/leave/{club}', 'leave')->name('club.leave');
        Route::get('student/club/notices/{club}', 'notice_index')->name('student.club.notice');

        // Canteen
        Route::get('student/canteen-menu', 'canteen_menu')->name('student.canteen.menu');
        Route::get('student/canteen-order', 'canteen_order_form')->name('student.canteen.order_form');
        Route::post('student/canteen-order-store', 'canteen_order_store')->name('student.canteen.order_store');
        Route::get('student/canteen-order-history', 'canteen_order_history')->name('student.canteen.order_history');
    });
    //Student routes end here

    //Common routes are here
    Route::controller(CommonController::class)->middleware('auth')->group(function () {

        //Filter object routes
        Route::get('section/{id}', 'classWiseSections')->name('class_wise_sections');
        Route::get('subjects/{id}', 'classWiseSubject')->name('class_wise_subject');
        Route::get('students/{id}', 'classWiseStudents')->name('class_wise_student');
        Route::get('class/section/{id}', 'sectionWiseStudents')->name('section_wise_students');
        Route::get('class/section/student/{id}', 'studentWiseParent')->name('student_wise_parent');

        //Grade crud routes
        Route::get('grade/get/{exam_mark}', 'getGrade')->name('get.grade');
        Route::patch('mark/update', 'markUpdate')->name('update.mark');

        Route::get('user/{id}', 'idWiseUserName')->name('id_wise_user_name');
    });
    //Common routes end here

    //Accountant routes are here
    Route::controller(AccountantController::class)->middleware('accountant', 'auth')->group(function () {

        Route::get('accountant/dashboard', 'accountantDashboard')->name('accountant.dashboard')->middleware('role_id');

        //Fee manager routes
        Route::get('accountant/student_fee_manager', 'studentFeeManagerList')->name('accountant.fee_manager.list');
        Route::get('accountant/student_fee_manager/export/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExport')->name('accountant.fee_manager.export');
        Route::get('accountant/student_fee_manager/pdf_print/{date_from}/{date_to}/{selected_class}/{selected_status}', 'feeManagerExportPdfPrint')->name('accountant.fee_manager.pdf_print');
        Route::get('accountant/fee_manager_create/{value}', 'createFeeManager')->name('accountant.fee_manager.open_modal');
        Route::post('accountant/fee_manager/{value}', 'feeManagerCreate')->name('accountant.create.fee_manager');
        Route::get('accountant/fee_manager/{id}', 'editFeeManager')->name('accountant.edit.fee_manager');
        Route::post('accountant/fee_manager_list/{id}', 'feeManagerUpdate')->name('accountant.fee_manager.update');
        Route::get('accountant/student_fee/delete/{id}', 'studentFeeDelete')->name('accountant.fee_manager.delete');
        Route::get('accountant/student_fee/invoice/{id}', 'studentFeeinvoice')->name('accountant.studentFeeinvoice');

        //Offline payment routes
        Route::get('accountant/offline_payment/pending', 'offline_payment_pending')->name('accountant.offline_payment_pending');
        Route::get('accountant/student_fee/delete/{id}/{status}', 'update_offline_payment')->name('accountant.update_offline_payment');

        //Expenses routes
        Route::get('accountant/expenses/list', 'expenseList')->name('accountant.expense.list');
        Route::get('accountant/expenses/create', 'createExpense')->name('accountant.expenses.open_modal');
        Route::post('accountant/expenses/added', 'expenseCreate')->name('accountant.create.expenses');
        Route::get('accountant/expenses/{id}', 'editExpense')->name('accountant.edit.expenses');
        Route::post('accountant/expenses/{id}', 'expenseUpdate')->name('accountant.expenses.update');
        Route::get('accountant/expenses/delete/{id}', 'expenseDelete')->name('accountant.expense.delete');

        //Expenses category routes
        Route::get('accountant/expense_category/list', 'expenseCategoryList')->name('accountant.expense.category_list');
        Route::get('accountant/expense_category/create', 'createExpenseCategory')->name('accountant.expense_category.open_modal');
        Route::post('accountant/expense_category/added', 'expenseCategoryCreate')->name('accountant.create.expense_category');
        Route::get('accountant/expense_category/{id}', 'editExpenseCategory')->name('accountant.edit.expense_category');
        Route::post('accountant/expense_category/{id}', 'expenseCategoryUpdate')->name('accountant.expense_category.update');
        Route::get('accountant/expense_category/delete/{id}', 'expenseCategoryDelete')->name('accountant.expense.category_delete');

        //Noticeboard routes
        Route::get('accountant/noticeboard', 'noticeboardList')->name('accountant.noticeboard.list');
        Route::get('accountant/noticeboard/{id}', 'editNoticeboard')->name('accountant.edit.noticeboard');

        //Event routes
        Route::get('accountant/events/list', 'eventList')->name('accountant.events.list');

        //Profile
        Route::get('accountant/profile', 'profile')->name('accountant.profile');
        Route::post('accountant/profile/update', 'profile_update')->name('accountant.profile.update');
        Route::any('accountant/password/{action_type}', 'password')->name('accountant.password');
        Route::post('accountant/language', 'user_language')->name('accountant.language');

        //Route::get('accountant/account_disable', 'account_disable')->name('accountant.account_disable');
        // Message
        Route::get('accountant/message/all-message/{id}', 'allMessage')->name('accountant.message.all_message');
        Route::get('accountant/message/message-thrades/{id}', 'messagethrades')->name('accountant.message.messagethrades');
        Route::post('accountant/message/single-chat/save', 'chat_save')->name('accountant.message.chat_save');
        Route::get('accountant/message/chat_empty', 'chat_empty')->name('accountant.message.chat_empty');
    });
    //Warden routes are here
    Route::controller(WardenController::class)->middleware('warden', 'auth')->group(function () {

        Route::get('warden/dashboard', 'wardenDashboard')->name('warden.dashboard')->middleware('role_id');

        // Hostel Room
        Route::get('warden/hostel-room-list', 'hostel_room_list')->name('warden.hostel.room_list');
        Route::get('warden/hostel-room-create', 'create_hostel_room')->name('warden.hostel.create_room');
        Route::post('warden/hostel-room-store', 'store_hostel_room')->name('warden.hostel.store_room');
        Route::get('warden/hostel-room-edit/{id}', 'edit_hostel_room')->name('warden.hostel.edit_room');
        Route::post('warden/hostel-room-update/{id}', 'update_hostel_room')->name('warden.hostel.update_room');
        Route::get('warden/hostel-room-delete/{id}', 'delete_hostel_room')->name('warden.hostel.delete_room');

        // Hostel Room Allocation
        Route::get('warden/hostel-room-allocation-list', 'hostel_room_allocation_list')->name('warden.hostel.allocation_list');
        Route::get('warden/hostel-room-allocation-create', 'create_hostel_room_allocation')->name('warden.hostel.create_allocation');
        Route::post('warden/hostel-room-allocation-store', 'store_hostel_room_allocation')->name('warden.hostel.store_allocation');
        Route::get('warden/hostel-room-allocation-edit/{id}', 'edit_hostel_room_allocation')->name('warden.hostel.edit_allocation');
        Route::post('warden/hostel-room-allocation-update/{id}', 'update_hostel_room_allocation')->name('warden.hostel.update_allocation');
        Route::get('warden/hostel-room-allocation-delete/{id}', 'delete_hostel_room_allocation')->name('warden.hostel.delete_allocation');
        Route::get('warden/hostel-applications', 'applications')->name('warden.hostel.applications');
        Route::get('warden/hostel-applications/approve/{id}', 'approveApplication')->name('warden.hostel.applications.approve');
        Route::get('warden/hostel-applications/reject/{id}', 'rejectApplication')->name('warden.hostel.applications.reject');

        Route::get('warden/hostel-fee-manager', 'hostelFees')->name('warden.hostel_fee_manager.list');
        Route::get('warden/hostel-fee/offline-payment/list', 'offlinePaymentList')->name('warden.offline.payment.hostel.list');

        Route::get('warden/hostel_fee/offline_payment/{id}', 'acceptOfflinePaymentHostel')->name('warden.accept.offline.payment.hostel');
        Route::get('warden/hostel_fee/offline_payment/reject/{id}', 'rejectOfflinePaymentHostel')->name('warden.reject.offline.payment.hostel');

        //Noticeboard routes
        Route::get('warden/noticeboard', 'noticeboardList')->name('warden.noticeboard.list');
        Route::get('warden/noticeboard/{id}', 'editNoticeboard')->name('warden.edit.noticeboard');

        //Event routes
        Route::get('warden/events/list', 'eventList')->name('warden.events.list');

        //Profile
        Route::get('warden/profile', 'profile')->name('warden.profile');
        Route::post('warden/profile/update', 'profile_update')->name('warden.profile.update');
        Route::any('warden/password/{action_type}', 'password')->name('warden.password');
        Route::post('warden/language', 'user_language')->name('warden.language');

        // Message
        Route::get('warden/message/all-message/{id}', 'allMessage')->name('warden.message.all_message');
        Route::get('warden/message/message-thrades/{id}', 'messagethrades')->name('warden.message.messagethrades');
        Route::post('warden/message/single-chat/save', 'chat_save')->name('warden.message.chat_save');
        Route::get('warden/message/chat_empty', 'chat_empty')->name('warden.message.chat_empty');
    });
    //Accountant routes end here

    //Librarian routes are here
    Route::controller(LibrarianController::class)->middleware('librarian', 'auth')->group(function () {

        Route::get('librarian/dashboard', 'librarianDashboard')->name('librarian.dashboard')->middleware('role_id');

        //Book routes
        Route::get('librarian/book/list', 'bookList')->name('librarian.book.book_list');
        Route::get('librarian/book/create', 'createBook')->name('librarian.book.open_modal');
        Route::post('librarian/book/added', 'bookCreate')->name('librarian.create.book');
        Route::get('librarian/book/{id}', 'editBook')->name('librarian.edit.book');
        Route::post('librarian/book/{id}', 'bookUpdate')->name('librarian.book.update');
        Route::get('librarian/book/delete/{id}', 'bookDelete')->name('librarian.book.delete');

        //Book issue routes
        Route::get('librarian/book_issue', 'bookIssueList')->name('librarian.book_issue.list');
        Route::get('librarian/book_issue/create', 'createBookIssue')->name('librarian.book_issue.open_modal');
        Route::post('librarian/book_issue/added', 'bookIssueCreate')->name('librarian.create.book_issue');
        Route::get('librarian/book_issue/{id}', 'editBookIssue')->name('librarian.edit.book_issue');
        Route::post('librarian/book_issue/{id}', 'bookIssueUpdate')->name('librarian.book_issue.update');
        Route::get('librarian/book_issue/return/{id}', 'bookIssueReturn')->name('librarian.book_issue.return');
        Route::get('librarian/book_issue/delete/{id}', 'bookIssueDelete')->name('librarian.book_issue.delete');

        //Noticeboard routes
        Route::get('librarian/noticeboard', 'noticeboardList')->name('librarian.noticeboard.list');
        Route::get('librarian/noticeboard/{id}', 'editNoticeboard')->name('librarian.edit.noticeboard');

        //Event routes
        Route::get('librarian/events/list', 'eventList')->name('librarian.events.list');

        //Profile
        Route::get('librarian/profile', 'profile')->name('librarian.profile');
        Route::post('librarian/profile/update', 'profile_update')->name('librarian.profile.update');
        Route::any('librarian/password/{action_type}', 'password')->name('librarian.password');
        Route::post('librarian/language', 'user_language')->name('librarian.language');
        Route::get('librarian/message/chat_empty', 'chat_empty')->name('librarian.message.chat_empty');
    });
    //Librarian routes end here

});
