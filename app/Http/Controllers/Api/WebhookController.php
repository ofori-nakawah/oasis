<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaystackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Handle Paystack webhook events.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handlePaystack(Request $request): JsonResponse
    {
        // Get the signature from header
        $signature = $request->header('X-Paystack-Signature');

        if (!$signature) {
            Log::warning('Paystack webhook missing signature');
            return response()->json(['message' => 'Missing signature'], 401);
        }

        // Get raw payload for signature verification
        $payload = $request->getContent();

        // Verify signature
        if (!$this->paystackService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Paystack webhook signature verification failed', [
                'signature' => $signature,
            ]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // Process the webhook event
        try {
            $eventData = $request->json()->all();
            
            Log::info('Paystack webhook received', [
                'event' => $eventData['event'] ?? 'unknown',
                'event_id' => $eventData['id'] ?? 'unknown',
            ]);

            $this->paystackService->processWebhookEvent($eventData);

            return response()->json(['message' => 'Webhook processed'], 200);
        } catch (\Exception $e) {
            Log::error('Paystack webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
