<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('role has users relationship', function () {
    $role = Role::factory()->create();
    User::factory()->count(2)->create(['role_id' => $role->id]);

    expect($role->users)->toHaveCount(2);
});

test('role can be created with factory', function () {
    $role = Role::factory()->create();

    expect($role->id)->not->toBeNull();
    expect($role->name)->not->toBeEmpty();
});

test('role admin state sets correct values', function () {
    $role = Role::factory()->admin()->create();

    expect($role->name)->toBe('admin');
    expect($role->description)->toBe('Administrator with full access');
});

test('role user state sets correct values', function () {
    $role = Role::factory()->user()->create();

    expect($role->name)->toBe('user');
    expect($role->description)->toBe('Regular user with standard permissions');
});
