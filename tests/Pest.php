<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(
    Tests\TestCase::class,
    RefreshDatabase::class,
)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeValidCode', function () {
    return $this->toMatch('/^OGN-\d{4}-[A-Z2-9]{6}$/');
});

/*
|--------------------------------------------------------------------------
| Global Hooks
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    // Seed roles before every test so spatie/permission works
    $roles = ['super_admin', 'accountant', 'secretariat', 'security'];
    foreach ($roles as $role) {
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name'       => $role,
            'guard_name' => 'web',
        ]);
    }
});
