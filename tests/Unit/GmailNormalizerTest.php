<?php

use Tests\TestUser;
use Aliziodev\GmailUnique\Services\GmailUniqueService;

it('normalizes gmail correctly', function () {
    $service = new GmailUniqueService();

    expect($service->normalize('j.o.k.o+test@gmail.com'))->toBe('joko@gmail.com');
    expect($service->normalize('Test.Email+spam@googlemail.com'))->toBe('testemail@googlemail.com');
    expect($service->normalize('non-gmail@example.com'))->toBe('non-gmail@example.com');
});

it('detects duplicate email using service class', function () {
    TestUser::create([
        'name' => 'Test User 6',
        'email' => 'aliziodev@gmail.com',
        'password' => bcrypt('password123')
    ]);

    $service = new GmailUniqueService();
    expect($service->isDuplicate('a.l.i.z.i.o.d.e.v@gmail.com', TestUser::class))->toBeTrue();
});