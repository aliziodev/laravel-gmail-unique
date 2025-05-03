<?php

use Tests\TestUser;
use Illuminate\Validation\ValidationException;

it('prevents saving duplicate email via model trait', function () {

    TestUser::create([
        'name' => 'Test User 1',
        'email' => 'aliziodev@gmail.com',
        'password' => bcrypt('password123')
    ]);

    $this->expectException(ValidationException::class);
    TestUser::create([
        'name' => 'Test User 2',
        'email' => 'a.l.i.z.i.o.d.e.v@gmail.com',
        'password' => bcrypt('password123')
    ]);
});

it('allows saving non-gmail duplicate variations', function () {

    TestUser::create([
        'name' => 'Test User 3',
        'email' => 'user@example.com',
        'password' => bcrypt('password123')
    ]);
    
    $user = TestUser::create([
        'name' => 'Test User 4',
        'email' => 'user@different-domain.com',
        'password' => bcrypt('password123')
    ]);
    
    expect($user)->toBeInstanceOf(TestUser::class);
    expect($user->email)->toBe('user@different-domain.com');
});

it('allows updating own email with same normalized version', function () {
    $user = TestUser::create([
        'name' => 'Test User 5',
        'email' => 'update.test@gmail.com',
        'password' => bcrypt('password123')
    ]);
    
    $user->email = 'up.date.te.st@gmail.com';
    $user->save();
    
    expect($user->email)->toBe('up.date.te.st@gmail.com');
});