<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('la page de réinitialisation de mot de passe peut être affichée', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('un lien de réinitialisation de mot de passe peut être demandé', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email_utili' => $user->email_utili]); // Changement ici

    Notification::assertSentTo($user, ResetPassword::class);
});

test('la page de réinitialisation de mot de passe peut être affichée', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email_utili' => $user->email_utili]); // Changement ici

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('le mot de passe peut être réinitialisé avec un token valide', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email_utili' => $user->email_utili]); // Changement ici

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email_utili' => $user->email_utili, // Changement ici
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});
