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
        $validated = $request->validate([
            'sender_name'  => ['required', 'string', 'max:191'],
            'sender_phone' => ['required', 'string', 'max:20'],
            'sender_email' => ['nullable', 'email', 'max:191'],
            'category'     => ['required', 'in:general,complaint,inquiry,payment'],
            'message'      => ['required', 'string', 'max:2000'],
        ]);

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
