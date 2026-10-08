<?php

namespace Tests\Feature;

use App\Models\Installation\Installation;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InstallationPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_photo_is_saved_preserved_and_replaced_on_installation_edit(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);
        Storage::fake('public');

        Role::create(['name' => 'data entry', 'guard_name' => 'web']);

        $supervisor = User::create([
            'pid' => 'USR-PHOTO-SUPERVISOR',
            'type' => 'data entry',
            'email' => 'photo-supervisor@example.com',
            'password' => 'password123',
        ]);
        $supervisor->assignRole('data entry');

        User::create([
            'pid' => 'USR-PHOTO-INSTALLER',
            'type' => 'data entry',
            'email' => 'photo-installer@example.com',
            'password' => 'password123',
        ]);

        DB::table('regions')->insert([
            'pid' => 'REG-PHOTO',
            'state' => 'Test State',
            'region' => 'Photo Region',
        ]);
        DB::table('teams')->insert([
            'region_pid' => 'REG-PHOTO',
            'team' => 'Photo Crew',
            'pid' => 'TEAM-PHOTO',
            'supervisor' => 'USR-PHOTO-SUPERVISOR',
        ]);
        DB::table('team_members')->insert([
            'region_pid' => 'REG-PHOTO',
            'team_pid' => 'TEAM-PHOTO',
            'user_pid' => 'USR-PHOTO-INSTALLER',
        ]);
        DB::table('meter_lists')->insert([
            'region_pid' => 'REG-PHOTO',
            'pid' => 'METER-LIST-PHOTO',
            'meter_number' => 'MTR-PHOTO-1',
            'status' => 1,
            'phase' => 'Single',
        ]);

        $payload = [
            'meter_number' => 'MTR-PHOTO-1',
            'preload' => '25',
            'state' => '1',
            'zone' => 'ZONE-PHOTO',
            'doi' => '2026-10-06',
            'upriser' => '2',
            'pole' => '1',
            'tariff' => 'R1',
            'advtariff' => 'R1',
            'fullname' => 'Photo Test Customer',
            'gsm' => '08012345678',
            'email' => 'customer@example.com',
            'premises' => 'RESIDENTIAL',
            'phase' => 'Red',
            'address' => '1 Test Street',
            'feeder_33kv' => 'F33-PHOTO',
            'feeder_11kv' => 'F11-PHOTO',
            'meter_type' => 'Single Phase',
            'meter_brand' => 'Test Brand',
            'account_no' => 'ACC-PHOTO-1',
            'business_unit' => 'MAP',
            'service_center' => 'North Service Center',
            'x_cordinate' => '9.12345',
            'y_cordinate' => '11.67890',
            'installer' => 'USR-PHOTO-INSTALLER',
            'seal' => '123456',
            'photo' => UploadedFile::fake()->image('installation.jpg'),
        ];

        $response = $this->actingAs($supervisor)
            ->withSession(['regionPid' => 'REG-PHOTO'])
            ->post('/record-form', $payload);

        $response->assertOk()->assertJsonPath('status', 201);

        $installation = Installation::where('meter_number', 'MTR-PHOTO-1')->firstOrFail();
        $originalPhoto = $installation->photo;
        Storage::disk('public')->assertExists($originalPhoto);

        $payload['pid'] = $installation->pid;
        unset($payload['photo']);
        $payload['fullname'] = 'Updated Photo Customer';

        $this->post('/record-form', $payload)
            ->assertOk()
            ->assertJsonPath('status', 201);

        $this->assertDatabaseHas('installations', [
            'pid' => $installation->pid,
            'fullname' => 'Updated Photo Customer',
            'photo' => $originalPhoto,
            'seal' => '123456',
            'service_center' => 'North Service Center',
        ]);
        Storage::disk('public')->assertExists($originalPhoto);

        $payload['photo'] = UploadedFile::fake()->image('replacement.jpg');

        $this->post('/record-form', $payload)
            ->assertOk()
            ->assertJsonPath('status', 201);

        $replacementPhoto = $installation->fresh()->photo;
        $this->assertNotSame($originalPhoto, $replacementPhoto);
        Storage::disk('public')->assertExists($replacementPhoto);
        Storage::disk('public')->assertMissing($originalPhoto);
    }
}
