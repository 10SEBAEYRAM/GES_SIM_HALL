<?php

test('la page d\'inscription peut être affichée', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('de nouveaux utilisateurs peuvent s\'inscrire', function () {
    $response = $this->post('/register', [
        'nom_utili' => 'Test User',  // Changement ici
        'email_utili' => 'test@example.com',  // Changement ici
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
