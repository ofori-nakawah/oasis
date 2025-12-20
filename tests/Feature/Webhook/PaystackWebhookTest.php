<?php

namespace Tests\Feature\Webhook;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaystackWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['services.paystack.secret_key' => 'test_secret_key']);
    }

    /** @test */
    public function webhook_accepts_valid_signature_and_processes_event()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'test_ref_123',
            'status' => Transaction::STATUS_PENDING,
        ]);

        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_123',
                'status' => 'success',
                'gateway_response' => 'Successful',
                'channel' => 'card',
                'customer' => [
                    'email' => $user->email,
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Webhook processed',
            ]);

        // Assert transaction status was updated
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_SUCCESS, $transaction->status);
        $this->assertNotNull($transaction->last_webhook_event);

        // Assert event was stored
        $this->assertDatabaseHas('transaction_events', [
            'transaction_id' => $transaction->id,
            'event_type' => 'charge.success',
            'processed' => true,
        ]);
    }

    /** @test */
    public function webhook_rejects_invalid_signature()
    {
        // Arrange
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_123',
            ],
        ];

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => 'invalid_signature',
        ]);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid signature',
            ]);
    }

    /** @test */
    public function webhook_rejects_request_without_signature()
    {
        // Arrange
        $payload = [
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_123',
            ],
        ];

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Missing signature',
            ]);
    }

    /** @test */
    public function webhook_handles_duplicate_events_idempotently()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'test_ref_123',
            'status' => Transaction::STATUS_PENDING,
        ]);

        $payload = [
            'id' => 'event_123',
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_123',
                'status' => 'success',
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Create existing event
        \App\Models\TransactionEvent::create([
            'transaction_id' => $transaction->id,
            'event_type' => 'charge.success',
            'paystack_event_id' => 'event_123',
            'payload' => $payload,
            'processed' => true,
        ]);

        // Act - Send same event again
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200);

        // Assert only one event exists
        $this->assertEquals(1, \App\Models\TransactionEvent::where('paystack_event_id', 'event_123')->count());
    }

    /** @test */
    public function webhook_updates_transaction_status_based_on_event_type()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'test_ref_123',
            'status' => Transaction::STATUS_PENDING,
        ]);

        $payload = [
            'id' => 'event_456',
            'event' => 'charge.failed',
            'data' => [
                'reference' => 'test_ref_123',
                'status' => 'failed',
                'gateway_response' => 'Declined',
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_FAILED, $transaction->status);
    }

    /** @test */
    public function webhook_processes_mobile_money_payment_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'mobile_ref_789',
            'status' => Transaction::STATUS_PENDING,
            'channel' => 'mobile_money',
        ]);

        $payload = [
            'id' => 'event_mobile_789',
            'event' => 'charge.success',
            'data' => [
                'reference' => 'mobile_ref_789',
                'status' => 'success',
                'gateway_response' => 'Successful',
                'channel' => 'mobile_money',
                'customer' => [
                    'email' => $user->email,
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Webhook processed',
            ]);

        // Assert transaction status was updated
        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_SUCCESS, $transaction->status);
        $this->assertEquals('mobile_money', $transaction->channel);
        $this->assertNotNull($transaction->paid_at);
        $this->assertNotNull($transaction->last_webhook_event);

        // Assert event was stored
        $this->assertDatabaseHas('transaction_events', [
            'transaction_id' => $transaction->id,
            'event_type' => 'charge.success',
            'paystack_event_id' => 'event_mobile_789',
            'processed' => true,
        ]);
    }

    /** @test */
    public function webhook_updates_mobile_money_channel_in_transaction()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'mobile_ref_999',
            'status' => Transaction::STATUS_PENDING,
            'channel' => 'mobile_money', // Initially set to mobile_money
        ]);

        $payload = [
            'id' => 'event_mobile_999',
            'event' => 'charge.success',
            'data' => [
                'reference' => 'mobile_ref_999',
                'status' => 'success',
                'gateway_response' => 'Successful',
                'channel' => 'mobile_money', // Confirmed from Paystack
                'customer' => [
                    'email' => $user->email,
                ],
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals('mobile_money', $transaction->channel);
        $this->assertEquals(Transaction::STATUS_SUCCESS, $transaction->status);
        $this->assertEquals('Successful', $transaction->gateway_response);
    }

    /** @test */
    public function webhook_handles_mobile_money_payment_failure()
    {
        // Arrange
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create([
            'user_id' => $user->id,
            'paystack_reference' => 'mobile_ref_failed',
            'status' => Transaction::STATUS_PENDING,
            'channel' => 'mobile_money',
        ]);

        $payload = [
            'id' => 'event_mobile_failed',
            'event' => 'charge.failed',
            'data' => [
                'reference' => 'mobile_ref_failed',
                'status' => 'failed',
                'gateway_response' => 'Insufficient funds',
                'channel' => 'mobile_money',
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = hash_hmac('sha512', $payloadJson, 'test_secret_key');

        // Act
        $response = $this->postJson('/api/v1/webhooks/paystack', $payload, [
            'X-Paystack-Signature' => $signature,
        ]);

        // Assert
        $response->assertStatus(200);

        $transaction->refresh();
        $this->assertEquals(Transaction::STATUS_FAILED, $transaction->status);
        $this->assertEquals('mobile_money', $transaction->channel);
        $this->assertEquals('Insufficient funds', $transaction->gateway_response);
        $this->assertNull($transaction->paid_at); // Should not have paid_at for failed transactions
    }
}

