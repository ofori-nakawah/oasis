<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $authorizationUrl = 'https://checkout.paystack.com/' . Str::random(20);
        
        return [
            'user_id' => User::factory(),
            'uuid' => Str::uuid()->toString(),
            'paystack_reference' => 'ref_' . Str::random(10),
            'client_reference' => Str::uuid()->toString(),
            'amount' => $this->faker->randomFloat(2, 10, 10000),
            'currency' => 'GHS',
            'email' => $this->faker->email(),
            'pay_link_url' => $authorizationUrl, // Required field from original migration
            'authorization_url' => $authorizationUrl,
            'access_code' => Str::random(20),
            'status' => Transaction::STATUS_PENDING,
            'gateway_response' => null,
            'channel' => 'card',
            'payment_type' => 'one-time',
            'paid_at' => null,
            'metadata' => null,
            'customer_data' => null,
            'last_webhook_event' => null,
        ];
    }

    /**
     * Indicate that the transaction is successful.
     */
    public function successful()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Transaction::STATUS_SUCCESS,
                'paid_at' => now(),
                'gateway_response' => 'Successful',
            ];
        });
    }

    /**
     * Indicate that the transaction failed.
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Transaction::STATUS_FAILED,
                'gateway_response' => 'Declined',
            ];
        });
    }
}
