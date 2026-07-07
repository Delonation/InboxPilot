<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmtpSetting extends Model
{
    protected $fillable = [
        'user_id',
        'host',
        'port',
        'encryption',
        'username',
        'password_encrypted',
        'from_name',
        'from_email',
        'reply_to_email',
        'last_test_passed_at',
        'last_test_error',
    ];

    protected function casts(): array
    {
        return [
            // Laravel transparently AES-256 encrypts on write / decrypts on read
            // using APP_KEY. The plaintext is only ever materialised in memory at
            // send/test time and is never serialised (see $hidden).
            'password_encrypted' => 'encrypted',
            'last_test_passed_at' => 'datetime',
        ];
    }

    /** Never expose the decrypted password through arrays/JSON. */
    protected $hidden = [
        'password_encrypted',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Human-readable summary safe to store on campaigns / show in reports. */
    public function summary(): string
    {
        return "{$this->host}:{$this->port} as {$this->from_email}";
    }
}
