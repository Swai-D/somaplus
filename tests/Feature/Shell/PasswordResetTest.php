<?php

use App\Enums\SubscriptionStatus;
use App\Models\Tenant;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    useTestHosts();
    Notification::fake();

    $this->acme = Tenant::factory()->create(['slug' => 'acme', 'name' => 'Acme Secondary']);
    $this->beta = Tenant::factory()->create(['slug' => 'beta']);
    $this->atAcme = makeSchoolUser($this->acme, ['email' => 'asha@example.test']);
    $this->atBeta = makeSchoolUser($this->beta, ['email' => 'asha@example.test']);

    $this->passwordOf = fn (User $user) => TenantContext::runAsSystem(fn () => User::findOrFail($user->id)->password);

    // Asks for a link and returns the token that was mailed to the person.
    $this->requestToken = function (string $host, User $user) {
        $this->post("http://{$host}/forgot-password", ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        return $token;
    };
});

it('shows the forgot-password page, naming the school', function () {
    $this->get('http://acme.somaplus.test/forgot-password')
        ->assertOk()
        ->assertSee('Acme Secondary');
});

it('links to it from the sign-in page', function () {
    $this->get('http://acme.somaplus.test/login')->assertSee('/forgot-password', false);
});

it('mails a reset link to a person of that school', function () {
    $this->post('http://acme.somaplus.test/forgot-password', ['email' => 'asha@example.test'])
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertSentTo($this->atAcme, ResetPasswordNotification::class);
    Notification::assertNotSentTo($this->atBeta, ResetPasswordNotification::class);
});

it('gives the same answer for an address that does not exist, and sends nothing', function () {
    $this->post('http://acme.somaplus.test/forgot-password', ['email' => 'nobody@example.test'])
        ->assertSessionHas('status', __('passwords.sent'))
        ->assertSessionHasNoErrors();

    Notification::assertNothingSent();
});

it('does not mail a person of another school', function () {
    makeSchoolUser($this->beta, ['email' => 'only-at-beta@example.test']);

    $this->post('http://acme.somaplus.test/forgot-password', ['email' => 'only-at-beta@example.test'])
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertNothingSent();
});

it('writes the mail in the language of the person asking', function (string $locale) {
    app()->setLocale($locale);
    $mail = (new ResetPasswordNotification('a-token'))->toMail($this->atAcme);

    expect($mail->introLines)->toContain(__('passwords.mail.intro', [], $locale))
        ->and(implode(' ', $mail->introLines))->toContain('/reset-password/a-token');
})->with(['en', 'sw']);

it('keeps the tokens of two schools apart, even for the same email address', function () {
    ($this->requestToken)('acme.somaplus.test', $this->atAcme);
    ($this->requestToken)('beta.somaplus.test', $this->atBeta);

    expect(DB::table('password_reset_tokens')->count())->toBe(2)
        ->and(DB::table('password_reset_tokens')->where('email', 'asha@example.test')->count())->toBe(0);
});

it('resets the password with the mailed link', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertRedirect('http://acme.somaplus.test/login')
        ->assertSessionHas('status', __('passwords.reset'));

    expect(Hash::check('a-new-password', ($this->passwordOf)($this->atAcme)))->toBeTrue();
});

it('can sign in with the new password afterwards', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ]);

    $this->post('http://acme.somaplus.test/login', ['email' => 'asha@example.test', 'password' => 'a-new-password'])
        ->assertRedirect('http://acme.somaplus.test');
    $this->assertAuthenticatedAs($this->atAcme);
});

it('does not let a link of one school change the password at another school', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);
    $before = ($this->passwordOf)($this->atBeta);

    $this->post('http://beta.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertSessionHasErrors(['email' => __('passwords.invalid')]);

    expect(($this->passwordOf)($this->atBeta))->toBe($before)
        ->and(Hash::check('a-new-password', ($this->passwordOf)($this->atAcme)))->toBeFalse();
});

it('does not let a school link work on the central address', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->post('http://somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertSessionHasErrors('email');
});

it('refuses a wrong token', function () {
    ($this->requestToken)('acme.somaplus.test', $this->atAcme);
    $before = ($this->passwordOf)($this->atAcme);

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => 'not-the-token',
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertSessionHasErrors(['email' => __('passwords.invalid')]);

    expect(($this->passwordOf)($this->atAcme))->toBe($before);
});

it('works once only', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);
    $data = [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ];

    $this->post('http://acme.somaplus.test/reset-password', $data)->assertSessionHasNoErrors();

    $this->post('http://acme.somaplus.test/reset-password', [...$data, 'password' => 'another-password', 'password_confirmation' => 'another-password'])
        ->assertSessionHasErrors('email');

    expect(Hash::check('a-new-password', ($this->passwordOf)($this->atAcme)))->toBeTrue();
});

it('stops working after an hour', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->travel(61)->minutes();

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertSessionHasErrors('email');
});

it('refuses a short password and one that is not confirmed', function () {
    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'different-password',
    ])->assertSessionHasErrors('password');
});

it('shows the reset form with the address from the link', function () {
    $this->get('http://acme.somaplus.test/reset-password/some-token?email=asha@example.test')
        ->assertOk()
        ->assertSee('value="some-token"', false)
        ->assertSee('asha@example.test');
});

it('works for a platform administrator on the central address', function () {
    $admin = User::factory()->superAdmin()->create(['email' => 'root@somaplus.test']);

    $this->post('http://somaplus.test/forgot-password', ['email' => 'root@somaplus.test']);

    $token = null;
    Notification::assertSentTo($admin, ResetPasswordNotification::class, function ($notification) use (&$token) {
        $token = $notification->token;

        return true;
    });

    $this->post('http://somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'root@somaplus.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertRedirect('http://somaplus.test/login');

    expect(Hash::check('a-new-password', User::findOrFail($admin->id)->password))->toBeTrue();
});

it('still works for a read-only school', function () {
    $this->acme->update(['subscription_status' => SubscriptionStatus::ReadOnly]);

    $token = ($this->requestToken)('acme.somaplus.test', $this->atAcme);

    $this->post('http://acme.somaplus.test/reset-password', [
        'token' => $token,
        'email' => 'asha@example.test',
        'password' => 'a-new-password',
        'password_confirmation' => 'a-new-password',
    ])->assertRedirect('http://acme.somaplus.test/login');
});

it('turns a signed-in person away from the forgot-password page', function () {
    $this->actingAs($this->atAcme)
        ->get('http://acme.somaplus.test/forgot-password')
        ->assertRedirect('http://acme.somaplus.test');
});
