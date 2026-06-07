<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Nama Pengguna atau NIS');
        $response->assertSee('Masuk');
        $response->assertSee('Masuk menggunakan NIS atau nama pengguna.');
        $response->assertDontSee('Presensi lebih cepat');
        $response->assertDontSee('Data mudah ditinjau');
        $response->assertDontSee('Akun dikelola sekolah');
        $response->assertDontSee('Username / NIS');
        $response->assertDontSee('Login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        Role::create(['name' => 'admin']);
        $user->assignRole('admin');

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'username' => 'Nama pengguna/NIS atau kata sandi tidak sesuai.',
        ]);
    }

    public function test_login_requires_username_and_password(): void
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => '',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'username' => 'Nama pengguna atau NIS wajib diisi.',
            'password' => 'Kata sandi wajib diisi.',
        ]);
    }

    public function test_inactive_guru_can_not_authenticate(): void
    {
        Role::create(['name' => 'guru']);

        $user = User::factory()->create([
            'is_active' => false,
        ]);
        $user->assignRole('guru');

        $response = $this->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors([
            'username' => 'Akun guru sedang nonaktif. Hubungi admin.',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
