<?php

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use App\Models\User;

beforeEach(function () {
    useTestHosts();

    $this->school = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->other = Tenant::factory()->create(['slug' => 'beta']);
    $this->teacher = makeSchoolUser($this->school, ['email' => 'teacher@acme.test'], 'teacher');
});

it('shows the sign-in page with the school name on a school address', function () {
    $this->get('http://acme.somaplus.test/login')
        ->assertOk()
        ->assertSee('Acme Secondary')
        ->assertSee('name="password"', false);
});

it('shows the sign-in page for the platform on the central address', function () {
    $this->get('http://somaplus.test/login')
        ->assertOk()
        ->assertSee(__('auth.subtitle_platform'));
});

it('sends a guest from the dashboard to the sign-in page of the same address', function () {
    $this->get('http://acme.somaplus.test/')->assertRedirect('http://acme.somaplus.test/login');
    $this->get('http://somaplus.test/')->assertRedirect('http://somaplus.test/login');
});

it('signs a school user in on their school address', function () {
    $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertRedirect('http://acme.somaplus.test');

    $this->assertAuthenticatedAs($this->teacher);
});

it('refuses a wrong password', function () {
    $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'wrong'])
        ->assertSessionHasErrors(['email' => __('auth.failed')]);

    $this->assertGuest();
});

it('requires an email address and a password', function () {
    $this->post('http://acme.somaplus.test/login', [])->assertSessionHasErrors(['email', 'password']);
});

it('does not let a school user sign in at another school', function () {
    $this->post('http://beta.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('does not let a school user sign in on the central address', function () {
    $this->post('http://somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('signs a platform administrator in on the central address only', function () {
    $admin = User::factory()->superAdmin()->create(['email' => 'root@somaplus.test']);

    $this->post('http://acme.somaplus.test/login', ['email' => 'root@somaplus.test', 'password' => 'password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->post('http://somaplus.test/login', ['email' => 'root@somaplus.test', 'password' => 'password'])
        ->assertRedirect('http://somaplus.test');
    $this->assertAuthenticatedAs($admin);
});

it('signs the same email address in at each of two schools as a different person', function () {
    $atBeta = makeSchoolUser($this->other, ['email' => 'shared@example.test']);
    makeSchoolUser($this->school, ['email' => 'shared@example.test']);

    $this->post('http://beta.somaplus.test/login', ['email' => 'shared@example.test', 'password' => 'password']);

    $this->assertAuthenticatedAs($atBeta);
});

it('lets a read-only school sign in and out', function () {
    $this->school->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertRedirect('http://acme.somaplus.test');

    $this->post('http://acme.somaplus.test/logout')->assertRedirect('http://acme.somaplus.test/login');
    $this->assertGuest();
});

it('turns a person who is already signed in away from the sign-in page', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/login')
        ->assertRedirect('http://acme.somaplus.test');
});

it('stops after five wrong attempts, even with the right password', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'wrong']);
    }

    $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('counts attempts at one school apart from another school', function () {
    makeSchoolUser($this->other, ['email' => 'teacher@acme.test']);

    foreach (range(1, 5) as $attempt) {
        $this->post('http://acme.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'wrong']);
    }

    $this->post('http://beta.somaplus.test/login', ['email' => 'teacher@acme.test', 'password' => 'password'])
        ->assertRedirect('http://beta.somaplus.test');
});

it('signs out and keeps the chosen language', function () {
    $this->actingAs($this->teacher)
        ->withSession(['locale' => 'sw'])
        ->post('http://acme.somaplus.test/logout')
        ->assertRedirect('http://acme.somaplus.test/login')
        ->assertSessionHas('locale', 'sw');

    $this->assertGuest();
});
