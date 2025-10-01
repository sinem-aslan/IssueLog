kear<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password');

    $component->call('login');

    $component
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong-password');

    $component->call('login');

    $component
        ->assertHasErrors()
        ->assertNoRedirect();

    $this->assertGuest();
});

test('navigation menu can be rendered', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user);

    $response = $this->get('/dashboard');

    $response->assertOk();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $component = Volt::test('layout.navigation');

    $component->call('logout');

    $component
        ->assertHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
});

test('users can update their profile information', function () {
    $user = User::factory()->create(['is_active' => true]);
    $this->actingAs($user);

    $component = Volt::test('profile.update-profile-information-form')
        ->set('name', 'New Name')
        ->set('email', 'newemail@example.com');

    $component->call('updateProfileInformation');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $user->refresh();
    $this->assertSame('New Name', $user->name);
    $this->assertSame('newemail@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});
test('users can change their password', function () {
    $user = User::factory()->create(['is_active' => true]);
    $this->actingAs($user);

    $component = Volt::test('profile.update-password-form')
        ->set('current_password', 'password')
        ->set('password', 'new-password')
        ->set('password_confirmation', 'new-password');

    $component->call('updatePassword');

    $component
        ->assertHasNoErrors()
        ->assertNoRedirect();

    $user->refresh();
    $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password', $user->password));
});
test('non-admin users cannot access admin-only pages', function () {
    $user = User::factory()->create(['is_active' => true, 'is_admin' => false]);
    $this->actingAs($user);

    $responseUsers = $this->get('/users');
    $responseDepartments = $this->get('/departments');

    $responseUsers->assertForbidden();
    $responseDepartments->assertForbidden();
});
test('inactive users cannot log in', function () {
    $user = User::factory()->create(['is_active' => false]);

    $component = Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password');

    $component->call('login');

    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
    $this->assertGuest();
});
