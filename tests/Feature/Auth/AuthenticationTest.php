<?php

use App\Models\User;

test('la page de connexion peut être affichée', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('les utilisateurs peuvent s\'authentifier via la page de connexion', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email_utili' => $user->email_utili,  // Changement ici
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('les utilisateurs ne peuvent pas s\'authentifier avec un mot de passe invalide', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email_utili' => $user->email_utili,  // Changement ici
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('les utilisateurs peuvent se déconnecter', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
