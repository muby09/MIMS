<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InstallationBulkUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
                $table->id();
                $table->string('state');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('meter_types')) {
            Schema::create('meter_types', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('meter_brands')) {
            Schema::create('meter_brands', function (Blueprint $table) {
                $table->id();
                $table->string('brand');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('trading_zones')) {
            Schema::create('trading_zones', function (Blueprint $table) {
                $table->id();
                $table->string('state_id');
                $table->string('zone');
                $table->string('pid')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feeder33s')) {
            Schema::create('feeder33s', function (Blueprint $table) {
                $table->id();
                $table->string('zone_pid');
                $table->string('name');
                $table->string('pid')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('feeder11s')) {
            Schema::create('feeder11s', function (Blueprint $table) {
                $table->id();
                $table->string('state_id');
                $table->string('zone_pid');
                $table->string('feeder_33_pid');
                $table->string('name');
                $table->string('pid')->unique();
                $table->timestamps();
            });
        }
    }

    public function test_bulk_installation_upload_returns_inertia_redirect(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::create(['name' => 'data entry', 'guard_name' => 'web']);

        $user = User::create([
            'pid' => 'USR-TEST-1',
            'type' => 'data entry',
            'email' => 'dataentry@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole('data entry');

        $file = UploadedFile::fake()->createWithContent(
            'installations.csv',
            "wrong_header,fullname\n" .
            "ABC,Test User\n"
        );

        $response = $this
            ->actingAs($user)
            ->from('/installations')
            ->post('/installations/bulk', ['file' => $file]);

        $response
            ->assertRedirect('/installations')
            ->assertSessionHas('error');
    }

    public function test_bulk_installation_upload_resolves_dropdown_values_from_template_names(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::create(['name' => 'data entry', 'guard_name' => 'web']);

        $user = User::create([
            'pid' => 'USR-IMPORT-1',
            'type' => 'data entry',
            'email' => 'import@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole('data entry');

        DB::table('regions')->insert([
            ['pid' => 'REG-001', 'state' => 'Gombe', 'region' => 'North'],
        ]);

        DB::table('states')->insert([
            ['id' => 1, 'state' => 'Gombe'],
        ]);

        DB::table('trading_zones')->insert([
            ['id' => 1, 'state_id' => '1', 'zone' => 'NORTH', 'pid' => 'ZONE-001'],
        ]);

        DB::table('feeder33s')->insert([
            ['id' => 1, 'zone_pid' => 'ZONE-001', 'name' => 'FEEDER-33A', 'pid' => 'F33-001'],
        ]);

        DB::table('feeder11s')->insert([
            ['id' => 1, 'state_id' => '1', 'zone_pid' => 'ZONE-001', 'feeder_33_pid' => 'F33-001', 'name' => 'FEEDER-11A', 'pid' => 'F11-001'],
        ]);

        DB::table('meter_types')->insert([
            ['id' => 1, 'type' => 'SINGLE PHASE'],
        ]);

        DB::table('meter_brands')->insert([
            ['id' => 1, 'brand' => 'LUMINOUS'],
        ]);

        DB::table('user_details')->insert([
            ['user_pid' => 'USR-IMPORT-1', 'gsm' => '08012345678', 'username' => 'installer_alpha', 'firstname' => 'Installer', 'lastname' => 'Alpha', 'gender' => 'Male', 'dob' => '1990-01-01', 'region_pid' => 'REG-001', 'address' => 'Test Street'],
        ]);

        DB::table('meter_lists')->insert([
            ['region_pid' => 'REG-001', 'pid' => 'MTRLIST-001', 'meter_number' => 'MTR-1001', 'status' => 1, 'phase' => 'Single', 'type' => 'Single Phase', 'brand' => 'Luminous'],
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'installations.csv',
            "meter_number,fullname,gsm,account_no,address,state,zone,pole,phase,premises,tariff,advtariff,feeder_33kv,feeder_11kv,meter_type,meter_brand,x_cordinate,y_cordinate,seal,business_unit,installer,preload\n" .
            "MTR-1001,John Doe,08012345678,ACC-001,No 1 Main Street,Gombe,NORTH,1,Single,Residential,R2,R2,FEEDER-33A,FEEDER-11A,SINGLE PHASE,LUMINOUS,9.12345,11.67890,12345,MAP,installer_alpha,25\n"
        );

        $response = $this
            ->actingAs($user)
            ->withSession(['regionPid' => 'REG-001'])
            ->from('/installations')
            ->post('/installations/bulk', ['file' => $file]);

        $response->assertRedirect('/installations');

        $this->assertDatabaseHas('installations', [
            'meter_number' => 'MTR-1001',
            'state' => '1',
            'trading_zone' => 'ZONE-001',
            'feeder_33kv' => 'F33-001',
            'feeder_11kv' => 'F11-001',
            'meter_type' => 'SINGLE PHASE',
            'meter_brand' => 'LUMINOUS',
            'installer' => 'USR-IMPORT-1',
        ]);
    }

    public function test_bulk_installation_upload_restores_missing_leading_zero_on_meter_number(): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class);

        Role::create(['name' => 'data entry', 'guard_name' => 'web']);

        $user = User::create([
            'pid' => 'USR-IMPORT-2',
            'type' => 'data entry',
            'email' => 'importzero@example.com',
            'password' => 'password123',
        ]);
        $user->assignRole('data entry');

        DB::table('regions')->insert([
            ['pid' => 'REG-002', 'state' => 'Gombe', 'region' => 'North'],
        ]);

        DB::table('states')->insert([
            ['id' => 1, 'state' => 'Gombe'],
        ]);

        DB::table('trading_zones')->insert([
            ['id' => 1, 'state_id' => '1', 'zone' => 'NORTH', 'pid' => 'ZONE-002'],
        ]);

        DB::table('feeder33s')->insert([
            ['id' => 1, 'zone_pid' => 'ZONE-002', 'name' => 'FEEDER-33A', 'pid' => 'F33-002'],
        ]);

        DB::table('feeder11s')->insert([
            ['id' => 1, 'state_id' => '1', 'zone_pid' => 'ZONE-002', 'feeder_33_pid' => 'F33-002', 'name' => 'FEEDER-11A', 'pid' => 'F11-002'],
        ]);

        DB::table('meter_types')->insert([
            ['id' => 1, 'type' => 'SINGLE PHASE'],
        ]);

        DB::table('meter_brands')->insert([
            ['id' => 1, 'brand' => 'LUMINOUS'],
        ]);

        DB::table('user_details')->insert([
            ['user_pid' => 'USR-IMPORT-2', 'gsm' => '08012345678', 'username' => 'installer_beta', 'firstname' => 'Installer', 'lastname' => 'Beta', 'gender' => 'Male', 'dob' => '1990-01-01', 'region_pid' => 'REG-002', 'address' => 'Test Street'],
        ]);

        DB::table('meter_lists')->insert([
            ['region_pid' => 'REG-002', 'pid' => 'MTRLIST-002', 'meter_number' => '0123456789012', 'status' => 1, 'phase' => 'Single', 'type' => 'Single Phase', 'brand' => 'Luminous'],
        ]);

        $file = UploadedFile::fake()->createWithContent(
            'installations.csv',
            "meter_number,fullname,gsm,account_no,address,state,zone,pole,phase,premises,tariff,advtariff,feeder_33kv,feeder_11kv,meter_type,meter_brand,x_cordinate,y_cordinate,seal,business_unit,installer,preload\n" .
            "123456789012,John Doe,08012345678,ACC-002,No 2 Main Street,Gombe,NORTH,1,Single,Residential,R2,R2,FEEDER-33A,FEEDER-11A,SINGLE PHASE,LUMINOUS,9.12345,11.67890,12346,MAP,installer_beta,25\n"
        );

        $response = $this
            ->actingAs($user)
            ->withSession(['regionPid' => 'REG-002'])
            ->from('/installations')
            ->post('/installations/bulk', ['file' => $file]);

        $response->assertRedirect('/installations');

        $this->assertDatabaseHas('installations', [
            'meter_number' => '0123456789012',
            'state' => '1',
            'trading_zone' => 'ZONE-002',
            'feeder_33kv' => 'F33-002',
            'feeder_11kv' => 'F11-002',
            'meter_type' => 'SINGLE PHASE',
            'meter_brand' => 'LUMINOUS',
            'installer' => 'USR-IMPORT-2',
        ]);
    }
}
