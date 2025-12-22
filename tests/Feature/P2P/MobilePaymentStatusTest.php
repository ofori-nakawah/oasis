<?php

namespace Tests\Feature\P2P;

use App\Models\JobApplication;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MobilePaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set Paystack secret key for testing
        config(['services.paystack.secret_key' => 'test_secret_key']);
    }

    /** @test */
    public function authenticated_user_can_check_payment_status_by_reference()
    {
        // Arrange
        $user = User::factory()->create();
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'ref_status_123',
            'client_reference' => 'client_ref_123',
            'status' => Transaction::STATUS_SUCCESS,
            'metadata' => [
                'post_id' => 'post_uuid_123',
                'application_id' => 'app_uuid_123',
                'payment_type' => 'initial',
            ],
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status?reference=ref_status_123');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'transaction_status',
                    'reference',
                    'payment_type',
                    'post_id',
                    'application_id',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'transaction_status' => 'success',
                    'payment_type' => 'initial',
                ],
            ]);
    }

    /** @test */
    public function payment_status_check_requires_authentication()
    {
        // Act
        $response = $this->getJson('/api/v1/p2p/payment-status?reference=ref_123');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function payment_status_check_validates_reference_parameter()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status');

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'message' => 'Reference is required',
            ]);
    }

    /** @test */
    public function payment_status_check_returns_not_found_for_invalid_reference()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status?reference=invalid_ref_123');

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'message' => 'Transaction not found',
            ]);
    }

    /** @test */
    public function payment_status_check_returns_pending_status_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'ref_pending_123',
            'status' => Transaction::STATUS_PENDING,
            'metadata' => [
                'post_id' => 'post_uuid_123',
                'application_id' => 'app_uuid_123',
                'payment_type' => 'initial',
            ],
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status?reference=ref_pending_123');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'transaction_status' => 'pending',
                ],
            ]);
    }

    /** @test */
    public function payment_status_check_returns_failed_status_correctly()
    {
        // Arrange
        $user = User::factory()->create();
        
        $transaction = Transaction::factory()->failed()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'ref_failed_123',
            'metadata' => [
                'post_id' => 'post_uuid_123',
                'application_id' => 'app_uuid_123',
                'payment_type' => 'initial',
            ],
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status?reference=ref_failed_123');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'transaction_status' => 'failed',
                ],
            ]);
    }

    /** @test */
    public function payment_status_check_verifies_with_paystack_for_pending_transactions()
    {
        // Arrange
        $user = User::factory()->create();
        
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'ref_verify_123',
            'status' => Transaction::STATUS_PENDING,
            'metadata' => [
                'post_id' => 'post_uuid_123',
                'application_id' => 'app_uuid_123',
                'payment_type' => 'initial',
            ],
        ]);

        $paystackVerifyResponse = [
            'status' => true,
            'data' => [
                'reference' => 'ref_verify_123',
                'status' => 'success',
                'amount' => 100000,
                'paid_at' => now()->toIso8601String(),
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response($paystackVerifyResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/p2p/payment-status?reference=ref_verify_123');

        // Assert
        $response->assertStatus(200);
        
        // Verify that transaction status was updated
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_SUCCESS, $transaction->status);
    }
}

