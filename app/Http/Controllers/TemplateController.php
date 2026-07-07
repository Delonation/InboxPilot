<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Services\ActivityLogger;
use App\Services\HtmlSanitizer;
use App\Services\PlaceholderRenderer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TemplateController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->templates()->latest();

        if ($search = trim((string) $request->get('q'))) {
            $query->where('name', 'like', "%{$search}%")->orWhere('subject', 'like', "%{$search}%");
        }

        return view('templates.index', [
            'templates' => $query->paginate(12)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('templates.create', ['template' => new EmailTemplate(['content_type' => 'html'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTemplate($request);

        $template = $request->user()->templates()->create($data);
        ActivityLogger::user($request->user()->id, 'template_created', $template->name);

        return redirect()->route('templates.index')->with('success', 'Template created.');
    }

    public function edit(Request $request, EmailTemplate $template): View
    {
        $this->authorizeTemplate($request, $template);

        return view('templates.edit', compact('template'));
    }

    public function update(Request $request, EmailTemplate $template): RedirectResponse
    {
        $this->authorizeTemplate($request, $template);
        $template->update($this->validateTemplate($request));

        return redirect()->route('templates.index')->with('success', 'Template updated.');
    }

    public function destroy(Request $request, EmailTemplate $template): RedirectResponse
    {
        $this->authorizeTemplate($request, $template);
        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    public function duplicate(Request $request, EmailTemplate $template): RedirectResponse
    {
        $this->authorizeTemplate($request, $template);

        $copy = $template->replicate();
        $copy->name = $template->name.' (copy)';
        $copy->save();

        return redirect()->route('templates.edit', $copy)->with('success', 'Template duplicated.');
    }

    /** Render the template with sample data inside an isolated preview. */
    public function preview(Request $request, EmailTemplate $template, PlaceholderRenderer $renderer, HtmlSanitizer $sanitizer): View
    {
        $this->authorizeTemplate($request, $template);

        $data = $renderer->sampleData();
        $subject = $renderer->render($template->subject, $data);

        $html = $template->isHtml()
            ? $sanitizer->clean($renderer->render((string) $template->html_body, $data))
            : null;

        $text = $renderer->render((string) ($template->plain_body ?? ''), $data);

        return view('templates.preview', compact('template', 'subject', 'html', 'text'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateTemplate(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'content_type' => ['required', 'in:html,plain'],
            'html_body' => ['nullable', 'required_if:content_type,html', 'string'],
            'plain_body' => ['nullable', 'required_if:content_type,plain', 'string'],
        ]);
    }

    private function authorizeTemplate(Request $request, EmailTemplate $template): void
    {
        abort_if($template->user_id !== $request->user()->id, 403);
    }
}
