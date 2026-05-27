<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    private array $categoryRouting = [
        'general'   => ['camp_director', 'secretariat'],
        'complaint' => ['camp_director'],
        'inquiry'   => ['super_admin', 'camp_director', 'secretariat', 'accountant'],
        'payment'   => ['accountant'],
        'question'  => ['camp_director', 'secretariat'],
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sender_name'  => ['required', 'string', 'max:191'],
            'sender_phone' => ['required', 'string', 'max:20'],
            'sender_email' => ['nullable', 'email', 'max:191'],
            'category'     => ['required', 'in:general,complaint,inquiry,payment,question'],
            'message'      => ['required', 'string', 'max:2000'],
        ]);

        $msg = ContactMessage::create($validated);

        Log::info('contact.message_received', [
            'id' => $msg->id, 'from' => $msg->sender_name, 'category' => $msg->category,
        ]);

        // Filament DB notifications to appropriate roles
        $targetRoles = $this->categoryRouting[$msg->category] ?? ['secretariat'];
        if (! in_array('super_admin', $targetRoles)) {
            $targetRoles[] = 'super_admin';
        }

        $labels = [
            'general'   => 'General Enquiry',
            'complaint' => 'Complaint',
            'inquiry'   => 'Inquiry',
            'payment'   => 'Payment Question',
            'question'  => 'Question',
        ];
        $label = $labels[$msg->category] ?? ucfirst($msg->category);

        $recipients = User::role(array_unique($targetRoles))->get();

        foreach ($recipients as $recipient) {
            Notification::make()
                ->title("New {$label} from {$msg->sender_name}")
                ->body(substr($msg->message, 0, 160) . (strlen($msg->message) > 160 ? '…' : ''))
                ->icon('heroicon-o-envelope')
                ->sendToDatabase($recipient);
        }

        return back()->with('contact_success',
            'Your message has been received. We will get back to you within 24 hours.');
    }
}
