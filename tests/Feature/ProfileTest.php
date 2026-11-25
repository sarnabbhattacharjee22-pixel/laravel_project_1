<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile/edit');

    $response->assertOk();
});

test('backend profile page renders its profile navigation link', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/admin/profile');

    $response
        ->assertOk()
        ->assertSee(route('admin.profile.index'))
        ->assertSee('Swal.fire', false)
        ->assertSeeText('Your password has been updated successfully.');
});

test('backend profile form submits to its post update route', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/profile')
        ->assertOk()
        ->assertSee(
            'action="'.route('admin.profile.update.own').'" method="POST"',
            false
        );

    $this->post(route('admin.profile.update.own'), [
        'previous_password' => 'incorrect-password',
        'new_password' => 'new-password',
        'confirm_password' => 'new-password',
    ])
        ->assertOk()
        ->assertSeeText('update data');

    $this->assertTrue(Hash::check('password', $user->fresh()->password));
});

test('backend profile update returns ok after changing the password', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.profile.update.own'), [
            'previous_password' => 'password',
            'new_password' => 'new-password',
            'confirm_password' => 'new-password',
        ])
        ->assertOk()
        ->assertSeeText('ok');

    $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
});

test('backend profile page requires authentication', function () {
    $this->get('/profile')->assertRedirect(route('login'));

    $user = User::factory()->create();

    $this->actingAs($user)->get('/profile')
        ->assertOk()
        ->assertSeeText('profile');
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile/edit');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile/edit');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile/edit')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile/edit');

    $this->assertNotNull($user->fresh());
});
