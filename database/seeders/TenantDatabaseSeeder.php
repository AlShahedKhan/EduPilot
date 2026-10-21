<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed a tenant database with its base data
     * (roles, currency, global settings, payment methods, language, ...).
     *
     * @return void
     */
    public function run()
    {
        $this->call(InitialDataSeeder::class);
    }
}