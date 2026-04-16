<?php

namespace Database\Seeders;

use App\Models\BadgeColorConfig;
use App\Models\CampSetting;
use App\Models\Church;
use App\Models\District;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            CampSettingsSeeder::class,
            DistrictAndChurchSeeder::class,
            BadgeColorSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
