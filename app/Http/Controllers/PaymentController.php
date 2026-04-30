<?php

namespace App\Http\Controllers;

use App\Enums\CamperCategory;
use App\Http\Requests\InitiatePaymentRequest;
use App\Jobs\PaystackWebhookJob;
use App\Models\RegistrationCode;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * POST /api/v1/payment/initiate
     *
     * Creates a PENDING code and returns the Paystack authorization URL.
     */
    public function initiate(InitiatePaymentRequest $request): JsonResponse
    {
        $category = CamperCategory::from($request->validated('category'));

        // Fetch fee for this category from settings
        $amountNaira = (int) setting("fee_{$category->value}", 5000);

        $result = $this->paymentService->initiatePaystackPayment(
            name:        $request->validated('name'),
            phone:       $request->validated('phone'),
            amountNaira: $amountNaira,
            category:    $request->validated('category'),
        );

        return response()->json([
            'success'           => true,
            'code'              => $result['code'],
            'authorization_url' => $result['authorization_url'],
            'amount'            => $amountNaira,
        ]);
    }

    /**
     * GET /api/v1/payment/status/{code}
     *
     * Polled by the Paystack callback page every 3 seconds.
     */
    public function status(string $code): JsonResponse
    {
        $registrationCode = RegistrationCode::where('code', $code)
            ->select(['code', 'status', 'payment_type', 'amount_paid'])
            ->first();

        if (! $registrationCode) {
            return response()->json(['success' => false, 'message' => 'Code not found.'], 404);
        }

        return response()->json([
            'success'      => true,
            'status'       => $registrationCode->status->value,
            'status_label' => $registrationCode->status->label(),
            'is_active'    => $registrationCode->isActive(),
        ]);
    }

    /**
     * POST /registration/pay-online (web form)
     *
     * Handles the online payment initiation from the browser pay-online form.
     * On success, redirects to Paystack. On failure, returns back with errors.
     */
    public function initiateWeb(\App\Http\Requests\InitiatePaymentRequest $request)
    {
        $category    = CamperCategory::from($request->validated('category'));
        $amountNaira = (int) setting("fee_{$category->value}", 5000);

        try {
            $result = $this->paymentService->initiatePaystackPayment(
                name:        $request->validated('name'),
                phone:       $request->validated('phone'),
                amountNaira: $amountNaira,
                category:    $request->validated('category'),
            );
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'general' => 'Could not connect to the payment gateway. Please try again.',
            ]);
        }

        return redirect()->away($result['authorization_url']);
    }

/*
* Receives Paystack webhook events.
* Responds 200 immediately and processes in queue — never blocks.
* Excluded from CSRF middleware in bootstrap/app.php.
*/
    public function webhook(Request $request): JsonResponse
    {
        $payload   = $request->getContent();
        $signature = $request->header('X-Paystack-Signature', '');

        // Verify HMAC-SHA512 signature
        if (! $this->paymentService->verifyPaystackWebhookSignature($payload, $signature)) {
            Log::warning('webhook.paystack_invalid_signature');
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $event = $request->input('event');
        $data  = $request->input('data', []);

        Log::info('webhook.paystack_received', [
            'event'     => $event,
            'reference' => $data['reference'] ?? 'none',
        ]);

        // Dispatch to queue — do not process inline
        PaystackWebhookJob::dispatch($event, $data);

        // Always return 200 immediately so Paystack doesn't retry
        return response()->json(['message' => 'Received.'], 200);
    }
}
