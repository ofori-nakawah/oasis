<?php

namespace Tests\Feature\Payment;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentInitializationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set Paystack secret key for testing
        config(['services.paystack.secret_key' => 'test_secret_key']);
    }

    /** @test */
    public function authenticated_user_can_initialize_payment_with_valid_data()
    {
        // Arrange
        $user = User::factory()->create();
        
        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/xxxxxxxxxxxxx',
                'access_code' => 'xxxxxxxxxxxxx',
                'reference' => 'test_reference_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 500000,
            'channel' => 'card',
            'email' => $user->email,
            'currency' => 'GHS',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'authorization_url',
                    'access_code',
                    'reference',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/xxxxxxxxxxxxx',
                ],
            ]);

        // Assert transaction was created in database
        // Amount is stored in main currency unit (500000 pesewas = 5000 GHS)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 5000.00, // 500000 pesewas = 5000 GHS
            'email' => $user->email,
            'status' => 'pending',
            'authorization_url' => 'https://checkout.paystack.com/xxxxxxxxxxxxx',
        ]);
    }

    /** @test */
    public function payment_initialization_requires_authentication()
    {
        // Act
        $response = $this->postJson('/api/v1/payments/initialize', [
            'amount' => 500000,
            'channel' => 'card',
            'email' => 'test@example.com',
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function payment_initialization_validates_required_fields()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', []);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount', 'channel', 'email']);
    }

    /** @test */
    public function payment_initialization_validates_amount_is_numeric_and_positive()
    {
        // Arrange
        $user = User::factory()->create();

        // Act - Test with invalid amount
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => -100,
            'channel' => 'card',
            'email' => $user->email,
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function payment_initialization_validates_channel_is_valid()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 500000,
            'channel' => 'invalid_channel',
            'email' => $user->email,
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['channel']);
    }

    /** @test */
    public function payment_initialization_handles_paystack_api_failure()
    {
        // Arrange
        $user = User::factory()->create();

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => false,
                'message' => 'Invalid API key',
            ], 401),
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 500000,
            'channel' => 'card',
            'email' => $user->email,
        ]);

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
            ]);
    }

    /** @test */
    public function payment_initialization_stores_metadata_when_provided()
    {
        // Arrange
        $user = User::factory()->create();
        
        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/xxxxxxxxxxxxx',
                'access_code' => 'xxxxxxxxxxxxx',
                'reference' => 'test_reference_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        $metadata = [
            'post_id' => 'uuid-123',
            'service_type' => 'job_payment',
        ];

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 500000, // Amount in main currency unit (will be converted to pesewas)
            'channel' => 'card',
            'email' => $user->email,
            'metadata' => $metadata,
        ]);

        // Assert
        $response->assertStatus(200);
        
        // Amount stored in main currency unit (500000 = 5000 GHS)
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 5000.00,
        ]);

        $transaction = \App\Models\Transaction::where('user_id', $user->id)->first();
        $this->assertEquals($metadata, $transaction->metadata);
    }

    /** @test */
    public function authenticated_user_can_initialize_mobile_money_payment()
    {
        // Arrange
        $user = User::factory()->create();
        
        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/mobile_money_xxxxx',
                'access_code' => 'mobile_access_code_123',
                'reference' => 'mobile_ref_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 100000, // 1000 GHS in pesewas
            'channel' => 'mobile_money',
            'email' => $user->email,
            'currency' => 'GHS',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'authorization_url',
                    'access_code',
                    'reference',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/mobile_money_xxxxx',
                ],
            ]);

        // Assert transaction was created with mobile_money channel
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 1000.00, // 100000 pesewas = 1000 GHS
            'email' => $user->email,
            'status' => 'pending',
            'channel' => 'mobile_money',
            'authorization_url' => 'https://checkout.paystack.com/mobile_money_xxxxx',
        ]);
    }

    /** @test */
    public function mobile_money_payment_initialization_stores_metadata_for_p2p_job()
    {
        // Arrange
        $user = User::factory()->create();
        
        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/mobile_money_xxxxx',
                'access_code' => 'mobile_access_code_123',
                'reference' => 'mobile_ref_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        $metadata = [
            'post_id' => 'p2p-job-uuid-456',
            'service_type' => 'job_payment',
            'job_type' => 'P2P',
        ];

        // Act
        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 50000, // 500 GHS in pesewas
            'channel' => 'mobile_money',
            'email' => $user->email,
            'currency' => 'GHS',
            'metadata' => $metadata,
        ]);

        // Assert
        $response->assertStatus(200);
        
        // Assert transaction was created with mobile_money channel and metadata
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'channel' => 'mobile_money',
        ]);

        $transaction = \App\Models\Transaction::where('user_id', $user->id)->first();
        $this->assertEquals('mobile_money', $transaction->channel);
        $this->assertEquals($metadata, $transaction->metadata);
        $this->assertEquals('p2p-job-uuid-456', $transaction->metadata['post_id']);
        $this->assertEquals('job_payment', $transaction->metadata['service_type']);
    }

    /** @test */
    public function mobile_money_channel_is_validated_correctly()
    {
        // Arrange
        $user = User::factory()->create();

        // Act - Test that mobile_money is accepted as valid channel
        $paystackResponse = [
            'status' => true,
            'message' => 'Authorization URL created',
            'data' => [
                'authorization_url' => 'https://checkout.paystack.com/xxxxx',
                'access_code' => 'access_code_123',
                'reference' => 'ref_123',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response($paystackResponse, 200),
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/payments/initialize', [
            'amount' => 500000,
            'channel' => 'mobile_money',
            'email' => $user->email,
        ]);

        // Assert - Should succeed, not return validation error
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);
    }
}

