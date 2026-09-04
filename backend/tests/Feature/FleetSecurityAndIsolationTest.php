<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FleetSecurityAndIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create([
            'name'  => 'Rental Owner A',
            'email' => 'owner_a@test.com',
        ]);

        $this->userB = User::factory()->create([
            'name'  => 'Rental Owner B',
            'email' => 'owner_b@test.com',
        ]);
    }

    /**
     * Test User A can store vehicle with valid tenant-aware photo_path.
     */
    public function test_user_can_create_vehicle_with_own_photo_path(): void
    {
        Sanctum::actingAs($this->userA);

        $response = $this->postJson('/api/vehicles', [
            'name'         => 'Avanza 2024',
            'brand'        => 'Toyota',
            'model_year'   => '2024',
            'daily_rate'   => 350000,
            'photo_path'   => "{$this->userA->id}/new/avanza.webp",
            'transmission' => 'matic',
            'capacity'     => 7,
            'fuel_type'    => 'bensin',
            'description'  => 'Mobil bersih dan wangi.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('vehicles', [
            'user_id'    => $this->userA->id,
            'name'       => 'Avanza 2024',
            'photo_path' => "{$this->userA->id}/new/avanza.webp",
        ]);
    }

    /**
     * Test User A can store vehicle with UUID-based non-predictable photo_path.
     */
    public function test_user_can_use_uuid_based_photo_path(): void
    {
        Sanctum::actingAs($this->userA);

        $uuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
        $path = "{$this->userA->id}/new/{$uuid}.webp";

        $response = $this->postJson('/api/vehicles', [
            'name'       => 'UUID Car',
            'daily_rate' => 350000,
            'photo_path' => $path,
        ]);

        $response->assertStatus(201);
        $this->assertEquals($path, $response->json('data.photo_path'));
    }

    /**
     * Test User B CANNOT hijack or use User A's photo_path.
     */
    public function test_user_b_cannot_use_user_a_photo_path(): void
    {
        Sanctum::actingAs($this->userB);

        // User B tries to claim User A's photo path
        $response = $this->postJson('/api/vehicles', [
            'name'       => 'Innova User B',
            'daily_rate' => 500000,
            'photo_path' => "{$this->userA->id}/new/avanza.webp",
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test directory traversal attacks in photo_path are blocked.
     */
    public function test_directory_traversal_in_photo_path_is_rejected(): void
    {
        Sanctum::actingAs($this->userA);

        $maliciousPaths = [
            "{$this->userA->id}/../other/avanza.webp",
            "/etc/passwd",
            "../{$this->userA->id}/new/photo.webp",
            "{$this->userA->id}/new/../../private.webp",
            "{$this->userA->id}/new/malicious.exe",
            "random_file_without_user_id.webp",
        ];

        foreach ($maliciousPaths as $badPath) {
            $response = $this->postJson('/api/vehicles', [
                'name'       => 'Bad Path Car',
                'daily_rate' => 300000,
                'photo_path' => $badPath,
            ]);

            $this->assertTrue(
                in_array($response->status(), [403, 422], true),
                "Expected 403 or 422 for path [{$badPath}], got {$response->status()}"
            );
        }
    }

    /**
     * Test User B cannot update User A's vehicle.
     */
    public function test_user_b_cannot_update_user_a_vehicle(): void
    {
        $vehicleA = Vehicle::create([
            'user_id'    => $this->userA->id,
            'name'       => 'Avanza Original',
            'daily_rate' => 350000,
            'photo_path' => "{$this->userA->id}/1/original.webp",
            'status'     => 'available',
        ]);

        Sanctum::actingAs($this->userB);

        $response = $this->putJson("/api/vehicles/{$vehicleA->id}", [
            'name'       => 'Hijacked Name',
            'photo_path' => "{$this->userB->id}/1/hacked.webp",
        ]);

        $response->assertStatus(403);
        $this->assertEquals('Avanza Original', $vehicleA->fresh()->name);
    }

    /**
     * Test video_url validates allowed platforms and generates safe embed url.
     */
    public function test_video_url_validation_and_safe_embed_generation(): void
    {
        Sanctum::actingAs($this->userA);

        // 1. Valid YouTube URL should be accepted
        $response = $this->postJson('/api/vehicles', [
            'name'       => 'Video Car',
            'daily_rate' => 400000,
            'video_url'  => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertStatus(201);
        $this->assertEquals(
            'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',
            $response->json('data.safe_video_embed_url')
        );

        // 2. Arbitrary / malicious URL must be rejected
        $badResponse = $this->postJson('/api/vehicles', [
            'name'       => 'Malicious Video Car',
            'daily_rate' => 400000,
            'video_url'  => 'https://evil-site.com/exploit.js',
        ]);

        $badResponse->assertStatus(422);
    }

    /**
     * Test Public Fleet API outputs only public data and hides sensitive fields.
     */
    public function test_public_fleet_api_isolation(): void
    {
        // Set rental owner to userA
        config(['services.rental_owner_id' => $this->userA->id]);

        Vehicle::create([
            'user_id'     => $this->userA->id,
            'name'        => 'Public Avanza',
            'brand'       => 'Toyota',
            'model_year'  => '2024',
            'daily_rate'  => 350000,
            'status'      => 'available',
            'notes'       => 'Secret internal maintenance note: radiator needs fix',
            'description' => 'Ready lepas kunci!',
            'photo_path'  => "{$this->userA->id}/1/avanza.webp",
        ]);

        // Unauthenticated call to /api/public/fleet
        $response = $this->getJson('/api/public/fleet');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'brand',
                    'model_year',
                    'transmission',
                    'capacity',
                    'fuel_type',
                    'daily_rate',
                    'daily_rate_formatted',
                    'status',
                    'status_label',
                    'photo_url',
                    'video_url',
                    'safe_video_embed_url',
                    'description',
                ],
            ],
        ]);

        $firstItem = $response->json('data.0');

        // MUST contain public fields
        $this->assertEquals('Public Avanza', $firstItem['name']);
        $this->assertEquals('Tersedia', $firstItem['status_label']);
        $this->assertEquals('Ready lepas kunci!', $firstItem['description']);

        // MUST NOT contain sensitive private fields
        $this->assertArrayNotHasKey('user_id', $firstItem);
        $this->assertArrayNotHasKey('notes', $firstItem);
        $this->assertArrayNotHasKey('income', $firstItem);
        $this->assertArrayNotHasKey('expense', $firstItem);
        $this->assertArrayNotHasKey('profit', $firstItem);
        $this->assertArrayNotHasKey('transactions', $firstItem);
        $this->assertArrayNotHasKey('created_at', $firstItem);
    }

    /**
     * Test changing vehicle status to 'rented' does NOT create financial transactions.
     */
    public function test_operational_status_change_creates_no_financial_transactions(): void
    {
        Sanctum::actingAs($this->userA);

        $vehicle = Vehicle::create([
            'user_id'    => $this->userA->id,
            'name'       => 'Test Avanza Status',
            'daily_rate' => 350000,
            'status'     => 'available',
        ]);

        $initialTxCount = Transaction::count();

        // Update status to rented
        $response = $this->putJson("/api/vehicles/{$vehicle->id}", [
            'status' => 'rented',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('rented', $vehicle->fresh()->status);

        // Transaction count must be exactly identical
        $this->assertEquals($initialTxCount, Transaction::count());
    }

    /**
     * Test user can update status to 'maintenance' (di bengkel) with existing photo_path.
     */
    public function test_user_can_update_vehicle_status_to_maintenance_with_existing_photo_path(): void
    {
        Sanctum::actingAs($this->userA);

        $vehicle = Vehicle::create([
            'user_id'    => $this->userA->id,
            'name'       => 'Toyota Agya Kuning',
            'daily_rate' => 200000,
            'status'     => 'available',
            'photo_path' => 'http://127.0.0.1:8000/storage/fleet/agya-kuning-bp1496mm.jpg',
        ]);

        // Simulating the edit form submission from frontend:
        // status changed to maintenance, photo_path sends existing value
        $response = $this->putJson("/api/vehicles/{$vehicle->id}", [
            'name'       => 'Toyota Agya Kuning',
            'status'     => 'maintenance',
            'daily_rate' => 200000,
            'photo_path' => 'http://127.0.0.1:8000/storage/fleet/agya-kuning-bp1496mm.jpg',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('maintenance', $vehicle->fresh()->status);
        $this->assertEquals('http://127.0.0.1:8000/storage/fleet/agya-kuning-bp1496mm.jpg', $vehicle->fresh()->photo_path);
    }
}
