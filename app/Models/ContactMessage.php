<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactMessage extends Model
{
    protected $fillable = [
        'sender_name', 'sender_phone', 'sender_email',
        'category', 'message', 'is_read', 'read_at',
    ];

    protected function casts(): array
    {
        return ['is_read' => 'boolean', 'read_at' => 'datetime'];
    }

    // ── Per-admin read tracking ───────────────────────────────────────────────

    public function readers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'contact_message_reads')
            ->withPivot('read_at');
    }

    /** Has the given (or current) user read this message? */
    public function isReadBy(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $this->readers()->where('user_id', $userId)->exists();
    }

    /** Mark as read for the current user only. */
    public function markReadFor(int $userId): void
    {
        $this->readers()->syncWithoutDetaching([
            $userId => ['read_at' => now()],
        ]);

        // Keep the legacy is_read column in sync (marked when ANY admin reads it)
        if (! $this->is_read) {
            $this->update(['is_read' => true, 'read_at' => now()]);
        }
    }

    /** Legacy helper — marks for current auth user. */
    public function markAsRead(): void
    {
        $this->markReadFor(auth()->id());
    }
}
