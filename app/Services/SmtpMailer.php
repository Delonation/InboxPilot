<?php

namespace App\Services;

use App\Models\SmtpSetting;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;

/**
 * Sends mail through a single user's SMTP credentials by constructing a fresh
 * Symfony transport per call. Nothing touches Laravel's global mailer, so one
 * user's SMTP can never bleed into another request. The decrypted password is
 * only ever held in memory for the duration of the send.
 */
class SmtpMailer
{
    /**
     * Build an isolated transport from stored SMTP settings.
     */
    public function transport(SmtpSetting $smtp): EsmtpTransport
    {
        // ssl => implicit TLS (smtps, usually 465); tls => STARTTLS (usually 587);
        // none => unencrypted. STARTTLS is requested by passing null (auto).
        $tls = match ($smtp->encryption) {
            'ssl' => true,
            'none' => false,
            default => null,
        };

        $transport = new EsmtpTransport($smtp->host, (int) $smtp->port, $tls);
        $transport->setUsername($smtp->username);
        $transport->setPassword($smtp->password_encrypted); // decrypted by the cast
        $transport->getStream()->setTimeout(20);

        return $transport;
    }

    /**
     * Send one message. Returns a normalised result the callers can log/store.
     *
     * @return array{success: bool, response: ?string, error: ?string}
     */
    public function send(SmtpSetting $smtp, string $toEmail, ?string $toName, string $subject, ?string $html, ?string $text): array
    {
        $transport = $this->transport($smtp);

        $email = (new Email())
            ->from($this->address($smtp->from_email, $smtp->from_name))
            ->to($toName ? "{$toName} <{$toEmail}>" : $toEmail)
            ->subject($subject);

        if (filled($smtp->reply_to_email)) {
            $email->replyTo($smtp->reply_to_email);
        }

        if (filled($html)) {
            $email->html($html);
        }
        if (filled($text)) {
            $email->text($text);
        }
        // Guarantee at least one body part.
        if (blank($html) && blank($text)) {
            $email->text('');
        }

        try {
            $mailer = new Mailer($transport);
            $sent = $mailer->send($email);

            return [
                'success' => true,
                'response' => $this->lastResponse($sent?->getDebug()),
                'error' => null,
            ];
        } catch (TransportExceptionInterface $e) {
            return [
                'success' => false,
                'response' => null,
                'error' => $this->friendlyError($e->getMessage()),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'response' => null,
                'error' => $this->friendlyError($e->getMessage()),
            ];
        }
    }

    /**
     * Send a test email to the user's own from-address.
     *
     * @return array{success: bool, response: ?string, error: ?string}
     */
    public function sendTest(SmtpSetting $smtp): array
    {
        return $this->send(
            $smtp,
            $smtp->from_email,
            $smtp->from_name,
            'InboxPilot SMTP test',
            '<p>This is a test email from InboxPilot. Your SMTP connection is working.</p>',
            'This is a test email from InboxPilot. Your SMTP connection is working.'
        );
    }

    private function address(string $email, ?string $name): string
    {
        return $name ? "{$name} <{$email}>" : $email;
    }

    private function lastResponse(?string $debug): ?string
    {
        if (blank($debug)) {
            return null;
        }

        // Keep only the final server acknowledgement lines, trimmed.
        $lines = array_values(array_filter(array_map('trim', explode("\n", $debug)), fn ($l) => str_starts_with($l, '<') || str_contains($l, '250')));

        return ActivityLogger::sanitize($lines ? end($lines) : trim($debug));
    }

    /**
     * Map common transport failures to friendly, secret-free guidance.
     */
    private function friendlyError(string $message): string
    {
        $lower = strtolower($message);

        return match (true) {
            str_contains($lower, 'authentication') || str_contains($lower, '535') =>
                'Authentication failed. Check your SMTP username and password.',
            str_contains($lower, 'connection could not be established'), str_contains($lower, 'connection refused'), str_contains($lower, 'timed out') =>
                'Could not connect to the SMTP server. Check the host, port, and that the port is not blocked (use 465 or 587, never 25).',
            str_contains($lower, 'ssl') || str_contains($lower, 'tls') =>
                'TLS/SSL negotiation failed. Try switching the encryption type (SSL for port 465, TLS for port 587).',
            str_contains($lower, 'relay') || str_contains($lower, 'sender address') =>
                'The server rejected the sender address. Make sure the from address matches an account your SMTP server is allowed to send for.',
            default => ActivityLogger::sanitize($message),
        };
    }
}
