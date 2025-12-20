<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitializePaymentRequest;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Initialize a payment transaction.
     *
     * @param InitializePaymentRequest $request
     * @return JsonResponse
     */
    public function initialize(InitializePaymentRequest $request): JsonResponse
    {
        try {
            // Amount is expected in smallest currency unit (pesewas for GHS)
            // No conversion needed as input is already in pesewas

            $data = [
                'user_id' => $request->user()->id,
                'email' => $request->email,
                'amount' => (int)$request->amount, // Amount in pesewas (smallest currency unit)
                'channel' => $request->channel,
                'currency' => $request->currency ?? 'GHS',
                'metadata' => $request->metadata,
            ];

            $result = $this->paystackService->initializeTransaction($data);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error('Payment initialization failed', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to initialize payment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a payment transaction.
     *
     * @param string $reference
     * @return JsonResponse
     */
    public function verify(string $reference): JsonResponse
    {
        try {
            $result = $this->paystackService->verifyTransaction($reference);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to verify transaction',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
