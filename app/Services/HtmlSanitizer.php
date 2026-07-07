<?php

namespace App\Services;

/**
 * Lightweight HTML sanitizer for user-authored email bodies and previews.
 *
 * Phase 1 keeps zero extra dependencies for shared hosting, so this removes the
 * highest-risk vectors (script execution, event handlers, javascript: URIs,
 * embedded objects) rather than running a full DOM allow-list. It is applied
 * before previewing and before sending, so stored content cannot execute in the
 * preview pane or in clients that run scripts.
 */
class HtmlSanitizer
{
    public function clean(string $html): string
    {
        // Remove script/style/iframe/object/embed blocks entirely.
        $html = preg_replace('#<\s*(script|style|iframe|object|embed|form)\b[^>]*>.*?<\s*/\s*\1\s*>#is', '', $html) ?? $html;

        // Remove any dangling/self-closing dangerous tags.
        $html = preg_replace('#<\s*/?\s*(script|style|iframe|object|embed|form|meta|link)\b[^>]*>#is', '', $html) ?? $html;

        // Strip inline event handlers: on*="..." / on*='...' / on*=value.
        $html = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html) ?? $html;

        // Neutralise javascript: and data: URIs in href/src.
        $html = preg_replace('#(href|src)\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*\2#i', '$1=$2#$2', $html) ?? $html;

        return $html;
    }
}
