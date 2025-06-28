<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get('/tasks');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the tasks', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/tasks');
    $response->assertStatus(200);
});
