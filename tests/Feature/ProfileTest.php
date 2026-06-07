<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_profile_photo_can_be_updated(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $photo = UploadedFile::fake()->image('profile.jpg', 400, 400)->size(256);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $photo,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertNotNull($user->photo);
        Storage::disk('public')->assertExists($user->photo);
    }

    public function test_siswa_profile_photo_syncs_to_student_profile(): void
    {
        Storage::fake('public');

        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => '1001',
        ]);
        $user->assignRole('siswa');

        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
        ]);

        $photo = UploadedFile::fake()->image('siswa.jpg', 400, 400)->size(256);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => $photo,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $student->refresh();

        $this->assertNotNull($user->photo);
        $this->assertSame($user->photo, $student->photo);
        Storage::disk('public')->assertExists($user->photo);
    }

    public function test_profile_photo_can_be_removed(): void
    {
        Storage::fake('public');

        $oldPhoto = UploadedFile::fake()
            ->image('old-profile.jpg', 400, 400)
            ->store('profile-photos', 'public');

        $user = User::factory()->create([
            'photo' => $oldPhoto,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'remove_photo' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNull($user->refresh()->photo);
        Storage::disk('public')->assertMissing($oldPhoto);
    }

    public function test_siswa_profile_photo_removal_syncs_to_student_profile(): void
    {
        Storage::fake('public');

        Role::create(['name' => 'siswa']);

        $class = SchoolClass::create(['name' => 'Kelas 1']);
        $oldPhoto = UploadedFile::fake()
            ->image('old-siswa.jpg', 400, 400)
            ->store('profile-photos', 'public');

        $user = User::factory()->create([
            'photo' => $oldPhoto,
            'username' => '1001',
        ]);
        $user->assignRole('siswa');

        $student = Student::create([
            'user_id' => $user->id,
            'class_id' => $class->id,
            'nis' => '1001',
            'gender' => 'Laki-laki',
            'photo' => $oldPhoto,
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'remove_photo' => '1',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNull($user->refresh()->photo);
        $this->assertNull($student->refresh()->photo);
        Storage::disk('public')->assertMissing($oldPhoto);
    }

    public function test_profile_page_does_not_offer_account_deletion(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertDontSee('Hapus Akun');
    }

    public function test_account_deletion_route_is_not_available(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertStatus(405);
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }
}
