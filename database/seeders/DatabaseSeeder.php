<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie Permission tables must exist before RolesSeeder runs.
        // Publish and migrate them automatically if missing.
        if (! Schema::hasTable('roles')) {
            $this->command->warn('Spatie Permission tables not found. Publishing and running migrations...');

            Artisan::call('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider',
                '--force'    => true,
            ]);

            Artisan::call('migrate', ['--force' => true]);

            $this->command->info('Spatie Permission migrations complete.');
        }

        // Clear cached roles/permissions before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->call([
            RolesSeeder::class,
            CampSettingsSeeder::class,
            DistrictAndChurchSeeder::class,
            BadgeColorSeeder::class,
            ClubRankSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
