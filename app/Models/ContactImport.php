<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactImport extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'total_rows',
        'imported',
        'skipped_duplicates',
        'invalid_emails',
        'failed_rows',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
