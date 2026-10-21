<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to');
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->default('sent')->comment('sent|failed');
            $table->text('error')->nullable();
            $table->string('mailable_class')->nullable();
            $table->string('tenant_id')->nullable()->comment('school slug if tenant email');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
