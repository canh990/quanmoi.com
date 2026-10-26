<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DoiMatKhauTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_update_password_with_empty_fields()
    {
        $user = User::factory()->create([
            'mat_khau' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update-password'), [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['current_password', 'password']);
    }

    public function test_user_cannot_update_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'mat_khau' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update-password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_user_cannot_update_password_with_short_new_password()
    {
        $user = User::factory()->create([
            'mat_khau' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update-password'), [
            'current_password' => 'password123',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_cannot_update_password_with_mismatched_confirmation()
    {
        $user = User::factory()->create([
            'mat_khau' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update-password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'mismatched123',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    public function test_user_can_update_password_successfully()
    {
        $user = User::factory()->create([
            'mat_khau' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->put(route('tai-khoan.update-password'), [
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('tai-khoan.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->mat_khau));
    }
}
