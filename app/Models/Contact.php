<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'first_name',
        'last_name',
        'phone',
        'company',
        'tags',
        'is_unsubscribed',
    ];

    protected function casts(): array
    {
        return [
            'is_unsubscribed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Active = not unsubscribed; eligible to receive campaigns. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_unsubscribed', false);
    }

    /** Simple comma-tag filter (phase 1 keeps tags denormalised). */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereRaw('FIND_IN_SET(?, REPLACE(tags, ", ", ","))', [$tag]);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /** Tags as a trimmed array. */
    public function tagList(): array
    {
        if (blank($this->tags)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $this->tags))));
    }
}
