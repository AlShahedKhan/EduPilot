<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    public function up()
    {
        // users
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // classes
        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // sections
        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // sessions
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // exams
        Schema::table('exams', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // daily_attendances
        Schema::table('daily_attendances', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // routines
        Schema::table('routines', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('room_id')->references('id')->on('class_rooms')->onDelete('set null');

            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // syllabuses
        Schema::table('syllabuses', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // student_fee_managers
        Schema::table('student_fee_managers', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // expense_categories
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // books
        Schema::table('books', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // book_issues
        Schema::table('book_issues', function (Blueprint $table) {
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // noticeboard
        Schema::table('noticeboard', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // frontend_events
        Schema::table('frontend_events', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });

        // exam_categories
        Schema::table('exam_categories', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // teacher_permissions
        Schema::table('teacher_permissions', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });

        // canteen_categories
        Schema::table('canteen_categories', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // canteen_items
        Schema::table('canteen_items', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('canteen_categories')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // canteen_orders
        Schema::table('canteen_orders', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ordered_by')->references('id')->on('users')->onDelete('set null');
        });

        // canteen_order_items
        Schema::table('canteen_order_items', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('canteen_orders')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('canteen_items')->onDelete('cascade');
        });

        // gradebooks
        Schema::table('gradebooks', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('exam_category_id')->references('id')->on('exam_categories')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // appraisals
        Schema::table('appraisals', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // appraisal_submits
        Schema::table('appraisal_submits', function (Blueprint $table) {
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('apprasial_id')->references('id')->on('appraisals')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // admit_cards
        Schema::table('admit_cards', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // chats
        Schema::table('chats', function (Blueprint $table) {
            $table->foreign('message_thrade')->references('id')->on('message_thrades')->onDelete('cascade');
            $table->foreign('reciver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('chats')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // feedback
        Schema::table('feedback', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });

        // message_thrades
        Schema::table('message_thrades', function (Blueprint $table) {
            $table->foreign('reciver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // payment_methods
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // hostels
        Schema::table('hostels', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('warden_id')->references('id')->on('users')->onDelete('set null');
        });

        // hostel_applications
        Schema::table('hostel_applications', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('hostel_rooms')->onDelete('set null');
        });

        // hostel_fees
        Schema::table('hostel_fees', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        // hostel_rooms
        Schema::table('hostel_rooms', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('hostel_id')->references('id')->on('hostels')->onDelete('cascade');
        });

        // hostel_room_allocations
        Schema::table('hostel_room_allocations', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('hostel_rooms')->onDelete('cascade');
        });

        // clubs
        Schema::table('clubs', function (Blueprint $table) {
            $table->foreign('advisor_id')->references('id')->on('users')->onDelete('set null');
        });

        // club_members
        Schema::table('club_members', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });

        // club_notices
        Schema::table('club_notices', function (Blueprint $table) {
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('advisor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        $tables = [
            'club_notices', 'club_members', 'clubs',
            'hostel_room_allocations', 'hostel_rooms', 'hostel_fees', 'hostel_applications', 'hostels',
            'payment_methods', 'payments',
            'message_thrades', 'feedback', 'chats', 'admit_cards',
            'appraisal_submits', 'appraisals',
            'gradebooks', 'canteen_order_items', 'canteen_orders', 'canteen_items', 'canteen_categories',
            'teacher_permissions',
            'exam_categories', 'frontend_events', 'noticeboard',
            'book_issues', 'books', 'expenses', 'expense_categories',
            'student_fee_managers', 'syllabuses', 'routines',
            'daily_attendances', 'enrollments', 'exams', 'subjects',
            'sessions', 'sections', 'classes', 'users',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(\Illuminate\Support\Facades\Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table->getTable()));
            });
        }
    }
}
