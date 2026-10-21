<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->string('btcl_email')->nullable()->after('domain');
            $table->string('btcl_phone')->nullable()->after('btcl_email');
            $table->string('btcl_password')->nullable()->after('btcl_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['btcl_email', 'btcl_phone', 'btcl_password']);
        });
    }
};
