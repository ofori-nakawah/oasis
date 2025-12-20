<?php

namespace Tests\Feature\Payment;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['services.paystack.secret_key' => 'test_secret_key']);
    }

    /** @test */
    public function authenticated_user_can_verify_transaction()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'test_ref_123',
            'status' => Transaction::STATUS_PENDING,
        ]);

        $paystackResponse = [
            'status' => true,
            'message' => 'Verification successful',
            'data' => [
                'reference' => 'test_ref_123',
                'status' => 'success',
                'amount' => 500000,
                'currency' => 'GHS',
                'gateway_response' => 'Successful',
            ],
        ];

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response($paystackResponse, 200),
        ]);

        // Act
        $response = $this->actingAs($user)->getJson("/api/v1/payments/verify/{$transaction->paystack_reference}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'reference' => 'test_ref_123',
                    'status' => 'success',
                ],
            ]);
    }

    /** @test */
    public function payment_verification_handles_invalid_reference()
    {
        // Arrange
        $user = User::factory()->create();

        Http::fake([
            'api.paystack.co/transaction/verify/invalid_ref' => Http::response([
                'status' => false,
                'message' => 'Transaction not found',
            ], 404),
        ]);

        // Act
        $response = $this->actingAs($user)->getJson('/api/v1/payments/verify/invalid_ref');

        // Assert
        $response->assertStatus(500)
            ->assertJson([
                'status' => false,
            ]);
    }

    /** @test */
    public function payment_verification_requires_authentication()
    {
        // Act
        $response = $this->getJson('/api/v1/payments/verify/test_ref');

        // Assert
        $response->assertStatus(401);
    }
}

