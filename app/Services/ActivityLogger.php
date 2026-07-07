<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\SmtpLog;
use App\Models\SystemLog;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * Single entry point for all audit/event logging. Centralising this keeps the
 * "never log secrets" rule enforceable in one place: callers pass plain values
 * and this class is the only writer of the log tables.
 */
class ActivityLogger
{
    public static function admin(int $adminId, string $action, ?int $targetUserId = null, ?string $details = null): void
    {
        AdminActivityLog::create([
            'admin_id' => $adminId,
            'target_user_id' => $targetUserId,
            'action' => $action,
            'details' => $details,
            'ip_address' => Request::ip(),
        ]);
    }

    public static function user(int $userId, string $action, ?string $details = null): void
    {
        UserActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Record an SMTP test or per-send result. The response/error are sanitised
     * before storage so credentials never leak into the logs.
     */
    public static function smtp(int $userId, string $context, bool $success, ?string $response = null, ?string $error = null, ?int $campaignId = null): void
    {
        SmtpLog::create([
            'user_id' => $userId,
            'context' => $context,
            'campaign_id' => $campaignId,
            'success' => $success,
            'response' => $response ? self::sanitize($response) : null,
            'error_message' => $error ? self::sanitize($error) : null,
        ]);
    }

    public static function system(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'message' => $message,
            'context' => $context ?: null,
        ]);
    }

    public static function exception(string $message, Throwable $e): void
    {
        self::system('error', $message, [
            'exception' => $e::class,
            'error' => self::sanitize($e->getMessage()),
        ]);
    }

    /**
     * Strip anything that looks like a credential from a free-text message
     * before it is persisted. Defence in depth on top of callers being careful.
     */
    public static function sanitize(string $text): string
    {
        $patterns = [
            '/(password|passwd|pwd|secret|token|api[_-]?key)\s*[:=]\s*\S+/i',
            '/AUTH\s+\S+/i',
        ];

        $clean = preg_replace($patterns, '$1 [redacted]', $text) ?? $text;

        return mb_strimwidth($clean, 0, 2000, '...');
    }
}
