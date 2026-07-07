@props(['status'])

@php
    // Maps any user / campaign / recipient status to a color + readable label.
    $map = [
        // user account
        'approved' => ['green', 'Approved'],
        'pending' => ['amber', 'Pending'],
        'rejected' => ['red', 'Rejected'],
        'suspended' => ['red', 'Suspended'],
        // campaign
        'draft' => ['gray', 'Draft'],
        'sending' => ['blue', 'Sending'],
        'completed' => ['green', 'Completed'],
        'completed_with_errors' => ['amber', 'Completed with errors'],
        'failed' => ['red', 'Failed'],
        // recipient
        'sent' => ['green', 'Sent'],
        'skipped_unsubscribed' => ['gray', 'Skipped (unsubscribed)'],
        'skipped_invalid' => ['gray', 'Skipped (invalid)'],
    ];

    [$color, $label] = $map[$status] ?? ['gray', ucfirst(str_replace('_', ' ', $status))];
@endphp

<x-badge :color="$color">{{ $label }}</x-badge>
