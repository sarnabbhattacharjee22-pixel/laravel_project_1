<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        ->assertSeeText('Profile details')
        ->assertSee('action="'.route('admin.profile.update.info').'"', false);
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

test('authenticated users can save their profile details and cover photo', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'username' => 'existing-handle',
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('admin.profile.index'))
        ->post(route('admin.profile.update.info'), [
            'name' => 'Updated Name',
            'username' => 'existing-handle',
            'bio' => 'Updated profile biography.',
            'cover_photo' => UploadedFile::fake()->image('cover.jpg'),
        ]);

    $response
        ->assertRedirect(route('admin.profile.index'))
        ->assertSessionHas('success', 'Your profile information has been updated successfully.');

    $updatedUser = $user->fresh();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'username' => 'existing-handle',
        'bio' => 'Updated profile biography.',
        'image' => $updatedUser->image,
    ]);

    Storage::disk('public')->assertExists($updatedUser->image);

    $this->actingAs($updatedUser)
        ->get(route('admin.profile.index'))
        ->assertOk()
        ->assertSee('Swal.fire', false)
        ->assertSee('Your profile information has been updated successfully.', false)
        ->assertSee('value="Updated Name"', false)
        ->assertSee('value="existing-handle"', false);
});

test('profile updates reject a username that belongs to another user', function () {
    $user = User::factory()->create([
        'username' => 'current-handle',
    ]);
    User::factory()->create([
        'username' => 'taken-handle',
    ]);

    $this->actingAs($user)
        ->from(route('admin.profile.index'))
        ->post(route('admin.profile.update.info'), [
            'name' => 'Changed Name',
            'username' => 'taken-handle',
            'bio' => 'This update should not be saved.',
        ])
        ->assertInvalid(['username' => 'already been taken']);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => $user->name,
        'username' => 'current-handle',
    ]);
});

test('guests cannot update profile information', function () {
    $this->post(route('admin.profile.update.info'), [
        'name' => 'Guest User',
        'username' => 'guest-user',
        'bio' => null,
    ])->assertRedirect(route('login'));
});
