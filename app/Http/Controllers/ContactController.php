<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request, SmsService $smsService)
    {
        $rules = [
            'sender_name'  => ['required', 'string', 'max:191'],
            'sender_phone' => ['required', 'string', 'max:20'],
            'sender_email' => ['nullable', 'email', 'max:191'],
            'category'     => ['required', 'in:general,complaint,inquiry,payment'],
            'message'      => ['required', 'string', 'max:2000'],
        ];

        // Validate reCAPTCHA if configured
        if (config('services.recaptcha.secret_key')) {
            $token = $request->input('g-recaptcha-response');
            if (! $token) {
                return back()->withInput()->withErrors(['recaptcha' => 'Please complete the reCAPTCHA verification.']);
            }
            $response = \Illuminate\Support\Facades\Http::post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);
            if (! ($response->json('success') ?? false)) {
                return back()->withInput()->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
            }
        }

        $validated = $request->validate($rules);

        $msg = ContactMessage::create($validated);

        Log::info('contact.message_received', [
            'id'       => $msg->id,
            'from'     => $msg->sender_name,
            'category' => $msg->category,
        ]);

        // Notify admin via SMS
        $adminPhone = setting('admin_phone') ?? setting('secretariat_phone');

        if ($adminPhone) {
            $categoryLabels = [
                'general'   => 'General Enquiry',
                'complaint' => 'Complaint',
                'inquiry'   => 'Inquiry',
                'payment'   => 'Payment Enquiry',
            ];

            $category = $categoryLabels[$msg->category] ?? $msg->category;

            $smsBody = "New message from {$msg->sender_name} ({$category}): "
                . substr($msg->message, 0, 20)
                . (strlen($msg->message) > 120 ? '...' : '')
                . " — Ogun Youth Congress";

            try {
                $smsService->send($adminPhone, $smsBody, 'contact_alert');
            } catch (\Throwable $e) {
                Log::warning('contact.sms_notification_failed', ['error' => $e->getMessage()]);
            }
        }

        return back()->with('contact_success',
            'Your message has been received. We will get back to you shortly.'
        );
    }
}
