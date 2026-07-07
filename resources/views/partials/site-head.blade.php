    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- If JS is disabled, don't hide the reveal-on-scroll sections. --}}
    <noscript><style>[data-reveal]{opacity:1 !important;transform:none !important;}</style></noscript>

    <style>
        /* ─────────────────────────────────────────────────────────────
           InboxPilot marketing page. Design idea: SMTP is a protocol with
           a visible dialogue. Mono/sans contrast is the texture. Flat,
           1px borders, single teal accent.
           ───────────────────────────────────────────────────────────── */
        :root {
            --ink: #0B1220;
            --paper: #FAFAF8;
            --surface: #FFFFFF;
            --line: #E6E4DE;
            --accent: #0E7C66;
            --accent-hover: #0B6553;
            --accent-soft: #E7F3F0;
            --amber: #B45309;

            --text: #0B1220;
            --muted: #56606E;
            --faint: #8A8F98;

            --ink-line: rgba(255,255,255,.10);
            --ink-muted: #97A2B0;
            --ink-faint: #6A7482;
            --accent-bright: #35B896;

            --r-card: 10px;
            --r-btn: 8px;
            --r-badge: 6px;
            --maxw: 1120px;

            --font-sans: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            --font-display: 'Bricolage Grotesque', 'Inter', system-ui, sans-serif;
            --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, monospace;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }
        html, body { overflow-x: clip; }
        /* Offset in-page anchors so the sticky navbar doesn't cover the heading */
        #features, #demo, #how { scroll-margin-top: 84px; }

        body {
            margin: 0; background: var(--paper); color: var(--text);
            font-family: var(--font-sans); font-size: 16px; line-height: 1.6;
            -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility;
        }

        ::selection { background: var(--accent); color: var(--paper); }
        ::-moz-selection { background: var(--accent); color: var(--paper); }

        [x-cloak] { display: none !important; }
        h1, h2, h3, p { margin: 0; }
        a { color: inherit; text-decoration: none; }
        img, svg { display: block; }
        .lucide { width: 20px; height: 20px; flex: none; }

        .container { width: 100%; max-width: var(--maxw); margin-inline: auto; padding-inline: 24px; }
        .section { padding-block: clamp(72px, 10vw, 128px); }

        /* Mono label system — the page's connective texture */
        .mono { font-family: var(--font-mono); }
        .mono-label, .eyebrow, .sec-anno {
            font-family: var(--font-mono); font-size: .72rem; font-weight: 500;
            letter-spacing: .14em; text-transform: uppercase;
        }
        .eyebrow { color: var(--accent); }
        .sec-anno { color: var(--faint); letter-spacing: .12em; white-space: nowrap; }

        .h2 {
            font-family: var(--font-display); font-weight: 600;
            font-size: clamp(1.9rem, 3.2vw, 2.6rem); letter-spacing: -.02em; line-height: 1.05; color: var(--text);
        }
        .lede { color: var(--muted); font-size: 1.05rem; }

        /* Section header: left on the edge, right-aligned spec annotation */
        .sec-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; }
        .sec-head .lede { margin-top: 14px; max-width: 42rem; }
        .sec-head .eyebrow { display: block; }
        .sec-head h2 { margin-top: 12px; }
        .sec-anno { padding-bottom: 7px; }
        @media (max-width: 760px) { .sec-anno { display: none; } }

        /* ── Buttons (mono caps) ─────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            min-height: 44px; padding: 11px 20px; border-radius: var(--r-btn); border: 1px solid transparent;
            font-family: var(--font-mono); font-size: .76rem; font-weight: 600; letter-spacing: .08em;
            text-transform: uppercase; line-height: 1; cursor: pointer; white-space: nowrap;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }
        .btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .btn-accent { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-accent:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn-ink { background: var(--ink); color: #fff; border-color: var(--ink); }
        .btn-ink:hover { background: #1a2333; }
        .btn-outline { background: transparent; color: var(--text); border-color: #D6D3CA; }
        .btn-outline:hover { background: #fff; border-color: #BFBBB0; }
        .btn-ghost { background: transparent; color: var(--muted); border-color: transparent; }
        .btn-ghost:hover { color: var(--text); background: rgba(11,18,32,.04); }
        .btn-light { background: #fff; color: var(--ink); border-color: #fff; }
        .btn-light:hover { background: #ECECE8; border-color: #ECECE8; }
        .btn-outline-light { background: transparent; color: #fff; border-color: rgba(255,255,255,.28); }
        .btn-outline-light:hover { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.5); }
        .btn:disabled { opacity: .55; cursor: default; }

        /* ── Badges ──────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 11px; border-radius: var(--r-badge);
            border: 1px solid var(--line); background: var(--surface);
            font-family: var(--font-mono); font-size: .7rem; font-weight: 500; letter-spacing: .1em;
            text-transform: uppercase; color: var(--muted);
        }
        .badge-accent { background: var(--accent-soft); border-color: rgba(14,124,102,.16); color: var(--accent); }
        .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); flex: none; }
        .dot-live { position: relative; }
        .dot-live::after { content: ""; position: absolute; inset: -4px; border-radius: 50%; border: 1px solid var(--accent); opacity: 0; animation: ping 2s ease-out infinite; }
        @keyframes ping { 0% { transform: scale(.6); opacity: .6; } 100% { transform: scale(1.6); opacity: 0; } }

        .spinner { width: 14px; height: 14px; border-radius: 50%; border: 2px solid currentColor; border-top-color: transparent; display: inline-block; animation: spin .7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Announcement bar (mono) ─────────────────────────────── */
        .announce { background: var(--ink); color: #fff; }
        .announce-inner { display: flex; align-items: center; justify-content: center; gap: 12px; padding-block: 9px; position: relative; }
        .announce-tag { font-family: var(--font-mono); font-size: .64rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--accent-bright); border: 1px solid rgba(53,184,150,.35); border-radius: 5px; padding: 2px 7px; }
        .announce-text { font-family: var(--font-mono); font-size: .72rem; letter-spacing: .04em; color: #C7CDD5; }
        .announce-link { font-family: var(--font-mono); font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; color: #fff; border-bottom: 1px solid rgba(255,255,255,.35); padding-bottom: 1px; }
        .announce-link:hover { border-color: #fff; }
        .announce-x { position: absolute; right: 0; top: 50%; transform: translateY(-50%); display: inline-flex; padding: 6px; border: 0; background: transparent; color: var(--ink-muted); cursor: pointer; border-radius: 6px; }
        .announce-x:hover { color: #fff; background: rgba(255,255,255,.08); }
        .announce-x .lucide { width: 16px; height: 16px; }

        /* ── Navbar ──────────────────────────────────────────────── */
        .nav { position: sticky; top: 0; z-index: 50; background: rgba(250,250,248,.8); backdrop-filter: saturate(150%) blur(8px); -webkit-backdrop-filter: saturate(150%) blur(8px); border-bottom: 1px solid transparent; transition: border-color .2s ease, background-color .2s ease; }
        .nav[data-scrolled="true"] { border-bottom-color: var(--line); background: rgba(250,250,248,.92); }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; padding-block: 16px; transition: padding .2s ease; }
        .nav[data-scrolled="true"] .nav-inner { padding-block: 11px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; font-family: var(--font-mono); font-weight: 600; font-size: .92rem; letter-spacing: .02em; }
        .brand-mark { width: 32px; height: 32px; border-radius: 9px; background: var(--ink); color: #fff; display: inline-flex; align-items: center; justify-content: center; }
        .brand-mark .lucide { width: 17px; height: 17px; }
        .brand-logo { width: 34px; height: 34px; border-radius: 8px; object-fit: cover; border: 1px solid var(--line); background: #fff; display: block; flex: none; }
        .nav-links { display: flex; align-items: center; gap: 30px; }
        .nav-links a { font-family: var(--font-mono); font-size: .72rem; font-weight: 500; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); transition: color .15s ease; }
        .nav-links a:hover { color: var(--text); }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .nav-burger { display: none; padding: 9px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); color: var(--text); cursor: pointer; }
        .nav-mobile { display: none; border-top: 1px solid var(--line); background: var(--paper); }
        .nav-mobile-inner { display: flex; flex-direction: column; gap: 4px; padding-block: 16px 20px; }
        .nav-mobile a.m-link { font-family: var(--font-mono); font-size: .78rem; letter-spacing: .1em; text-transform: uppercase; padding: 12px; border-radius: 8px; color: var(--text); }
        .nav-mobile a.m-link:hover { background: rgba(11,18,32,.04); }
        .nav-mobile .m-actions { display: flex; flex-direction: column; gap: 10px; margin-top: 10px; }
        .nav-mobile .btn { width: 100%; }

        /* ── Hero ────────────────────────────────────────────────── */
        .hero { position: relative; padding-block: clamp(56px, 8vw, 104px); overflow: hidden; }
        .hero::before {
            content: ""; position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background-image: repeating-linear-gradient(to bottom, transparent 0, transparent 33px, rgba(11,18,32,.035) 33px, rgba(11,18,32,.035) 34px);
            -webkit-mask-image: linear-gradient(to bottom, #000 60%, transparent);
            mask-image: linear-gradient(to bottom, #000 60%, transparent);
        }
        .hero .container { position: relative; z-index: 1; }
        .hero-grid { display: grid; grid-template-columns: 1.02fr .98fr; gap: clamp(40px, 5vw, 64px); align-items: center; }
        .hero h1 { font-family: var(--font-display); font-weight: 600; font-size: clamp(44px, 6.5vw, 84px); line-height: .98; letter-spacing: -.03em; color: var(--text); margin-top: 22px; }
        .hero h1 .accent { color: var(--accent); }
        .hero-sub { margin-top: 22px; max-width: 33em; color: var(--muted); font-size: 1.075rem; }
        .hero-cta { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 30px; }
        .hero-proof { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 28px; }
        .hero-proof span { display: inline-flex; align-items: center; gap: 8px; font-family: var(--font-mono); font-size: .7rem; font-weight: 500; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); }
        .hero-proof .lucide { width: 16px; height: 16px; color: var(--accent); }

        /* Hero: live SMTP session transcript (log-paper) */
        .smtp-wrap { position: relative; }
        .smtp { position: relative; z-index: 1; background: var(--surface); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; box-shadow: 0 24px 48px -24px rgba(11,18,32,.20); }
        .smtp-bar { display: flex; align-items: center; gap: 8px; padding: 12px 14px; border-bottom: 1px solid var(--line); background: #FBFBF9; }
        .tl { width: 12px; height: 12px; border-radius: 50%; }
        .tl-r { background: #FF5F57; } .tl-y { background: #FEBC2E; } .tl-g { background: #28C840; }
        .smtp-bar .t { margin-left: 8px; font-family: var(--font-mono); font-size: .72rem; letter-spacing: .04em; color: var(--faint); }
        .smtp-body {
            position: relative; font-family: var(--font-mono); font-size: .8rem; line-height: 2; padding: 16px 20px 18px; min-height: 360px;
            background-image: repeating-linear-gradient(to bottom, transparent 0, transparent calc(2em - 1px), rgba(11,18,32,.045) calc(2em - 1px), rgba(11,18,32,.045) 2em);
            background-position: 0 16px;
        }
        .smtp-line { display: none; white-space: pre-wrap; word-break: break-word; }
        .smtp-line.on { display: block; }
        .c-cmd { color: var(--text); }
        .c-res { color: var(--accent); font-weight: 600; }
        .c-dim { color: var(--muted); }
        .c-note { color: var(--faint); }
        .smtp-cursor { display: inline-block; width: 8px; height: 1.05em; background: var(--accent); vertical-align: -2px; animation: blink 1.1s steps(1) infinite; }
        @keyframes blink { 50% { opacity: 0; } }

        /* ── Config strip (replaces marquee) ─────────────────────── */
        .config { border-block: 1px solid var(--line); background: #F5F4F0; }
        .config .container { padding-block: 22px; }
        .config-cap { font-family: var(--font-mono); text-transform: uppercase; color: var(--accent); font-size: .66rem; font-weight: 500; letter-spacing: .18em; }
        .config-list { margin-top: 12px; font-family: var(--font-mono); font-size: .82rem; color: var(--muted); line-height: 1.9; letter-spacing: .01em; }
        .config-list .sep { color: #C9C6BD; padding: 0 4px; }

        /* ── Feature toolkit ─────────────────────────────────────── */
        .toolkit { display: grid; grid-template-columns: 300px 1fr; gap: 20px; margin-top: 44px; align-items: start; }
        .tablist { display: flex; flex-direction: column; gap: 10px; }
        .tab { display: flex; align-items: center; gap: 13px; text-align: left; width: 100%; padding: 15px 16px; border-radius: var(--r-card); border: 1px solid var(--line); background: var(--surface); color: var(--text); cursor: pointer; font-family: inherit; transition: border-color .15s ease, background-color .15s ease; }
        .tab:hover { border-color: #D6D3CA; }
        .tab:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .tab[aria-selected="true"] { background: var(--ink); border-color: var(--ink); color: #fff; }
        .tab-ico { width: 38px; height: 38px; border-radius: 9px; background: #F1F0EB; color: var(--ink); display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .tab[aria-selected="true"] .tab-ico { background: rgba(255,255,255,.12); color: #fff; }
        .tab-ico .lucide { width: 19px; height: 19px; }
        .tab b { display: block; font-family: var(--font-mono); font-size: .78rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
        .tab small { display: block; font-size: .8rem; opacity: .72; margin-top: 3px; }
        .panels { position: relative; }
        .panel { border: 1px solid var(--line); border-radius: var(--r-card); background: var(--surface); padding: clamp(24px, 3vw, 36px); }
        .panel[hidden] { display: none; }
        .panel h3 { font-size: 1.3rem; font-weight: 600; letter-spacing: -.01em; }
        .panel p { margin-top: 12px; color: var(--muted); max-width: 46em; }
        .panel ul { list-style: none; padding: 0; margin: 22px 0 0; display: flex; flex-direction: column; gap: 12px; }
        .panel li { display: flex; align-items: center; gap: 11px; font-size: .92rem; color: var(--text); }
        .panel li .tick { width: 22px; height: 22px; border-radius: 50%; background: var(--accent-soft); color: var(--accent); display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .panel li .tick .lucide { width: 13px; height: 13px; }

        /* ── Terminal test section ───────────────────────────────── */
        .dark { background: var(--ink); color: #fff; }
        .term-section { position: relative; overflow: hidden; }
        .term-code { position: absolute; font-family: var(--font-mono); font-size: 3.4rem; font-weight: 600; color: rgba(151,162,176,.14); letter-spacing: -.02em; z-index: 0; pointer-events: none; }
        .term-code.c1 { top: 8%; right: 6%; }
        .term-code.c2 { top: 44%; left: 3%; }
        .term-code.c3 { bottom: 10%; right: 12%; }
        .term-grid { position: relative; z-index: 1; display: grid; grid-template-columns: 1fr 1fr; gap: clamp(40px, 6vw, 72px); align-items: center; }
        .dark .eyebrow { color: var(--accent-bright); }
        .dark .h2 { color: #fff; }
        .term-copy p { margin-top: 16px; color: var(--ink-muted); max-width: 32em; }
        .term-anno { margin-top: 20px; font-family: var(--font-mono); font-size: .7rem; letter-spacing: .12em; text-transform: uppercase; color: var(--ink-faint); }
        .terminal { border: 1px solid var(--ink-line); border-radius: 12px; background: #070B12; overflow: hidden; }
        .term-bar { display: flex; align-items: center; gap: 8px; padding: 12px 14px; border-bottom: 1px solid var(--ink-line); }
        .term-title { margin-left: 8px; font-family: var(--font-mono); font-size: .78rem; color: var(--ink-faint); }
        .term-body { padding: 18px; font-family: var(--font-mono); font-size: .85rem; }
        .tline { display: flex; align-items: center; gap: 11px; min-height: 26px; }
        .tline .glyph { width: 18px; height: 18px; display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .tline .glyph .lucide { width: 13px; height: 13px; }
        .tline .glyph .ok { width: 18px; height: 18px; border-radius: 50%; background: rgba(53,184,150,.16); color: var(--accent-bright); display: inline-flex; align-items: center; justify-content: center; }
        .tline .glyph .spinner { color: var(--accent-bright); width: 13px; height: 13px; }
        .tline .glyph .idle { width: 5px; height: 5px; border-radius: 50%; background: #2A3444; }
        .tline .txt { color: #5C6675; transition: color .2s ease; }
        .tline[data-state="active"] .txt { color: #EAEEF3; }
        .tline[data-state="done"] .txt { color: #AEB8C6; }
        .tline.success[data-state="done"] .txt { color: var(--accent-bright); }
        .term-run { margin-top: 16px; }
        .term-run .btn { width: 100%; background: var(--accent); color: #fff; border-color: var(--accent); }
        .term-run .btn:hover:not(:disabled) { background: var(--accent-hover); }

        /* ── Server-receipt ledger (replaces stats) ──────────────── */
        .ledger { margin-top: 8px; border-top: 1px solid var(--line); }
        .ledger-row { display: flex; align-items: baseline; font-family: var(--font-mono); font-size: .86rem; padding: 15px 2px; border-bottom: 1px solid var(--line); }
        .ledger-row .k { color: var(--muted); white-space: nowrap; }
        .ledger-row .lead { flex: 1 1 auto; min-width: 18px; margin: 0 10px; border-bottom: 1px dotted #C7C4BB; transform: translateY(-4px); }
        .ledger-row .v { color: var(--text); white-space: nowrap; text-align: right; }
        .ledger-row .v .hl { color: var(--accent); }

        /* ── Session timeline (replaces step cards) ──────────────── */
        .timeline { margin-top: 40px; position: relative; padding-left: 4px; }
        .tl-rail { position: absolute; left: 4px; top: 6px; bottom: 6px; width: 1px; background: var(--line); }
        .tl-entry { position: relative; display: flex; align-items: baseline; gap: 18px; padding: 0 0 26px 26px; }
        .tl-entry:last-child { padding-bottom: 0; }
        .tl-node { position: absolute; left: 0; top: 7px; width: 9px; height: 9px; border-radius: 50%; background: var(--paper); border: 1.5px solid var(--accent); transform: translateX(-4px); }
        .tl-marker { font-family: var(--font-mono); font-size: .82rem; font-weight: 600; color: var(--accent); flex: none; width: 108px; }
        .tl-text { color: var(--muted); font-size: .98rem; }
        @media (max-width: 560px) {
            .tl-entry { flex-direction: column; gap: 4px; }
            .tl-marker { width: auto; }
        }

        /* ── Final CTA: full-width dark band ─────────────────────── */
        .cta-band { background: var(--ink); }
        .cta-inner { display: flex; align-items: flex-end; justify-content: space-between; gap: 32px; padding-block: clamp(56px, 8vw, 96px); }
        .cta-band h2 { font-family: var(--font-display); font-weight: 600; font-size: clamp(1.9rem, 3.6vw, 2.6rem); letter-spacing: -.02em; color: #fff; }
        .cta-band p { margin-top: 14px; max-width: 32em; color: var(--ink-muted); }
        .cta-btns { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .cta-sign { font-family: var(--font-mono); font-size: .8rem; color: var(--ink-faint); white-space: nowrap; padding-bottom: 4px; }
        .cta-sign .code { color: var(--accent-bright); }
        @media (max-width: 820px) { .cta-inner { flex-direction: column; align-items: flex-start; } .cta-sign { white-space: normal; } }

        /* ── Footer ──────────────────────────────────────────────── */
        .footer { border-top: 1px solid var(--line); }
        .footer-inner { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding-block: 30px; }
        .footer-brand { display: inline-flex; align-items: center; gap: 9px; font-family: var(--font-mono); font-weight: 600; font-size: .82rem; letter-spacing: .04em; }
        .footer-brand .brand-mark { width: 28px; height: 28px; border-radius: 8px; }
        .footer-brand .brand-mark .lucide { width: 15px; height: 15px; }
        .footer-brand .brand-logo { width: 30px; height: 30px; }
        .footer p { font-size: .82rem; color: var(--faint); max-width: 30em; text-align: right; }

        /* DLN Web Studio credit strip (dark, so the white logo reads) */
        .colophon { background: var(--ink); border-top: 1px solid var(--ink-line); }
        .colophon-inner { display: flex; align-items: center; justify-content: center; gap: 12px; padding-block: 18px; }
        .colophon-link { display: inline-flex; align-items: center; gap: 12px; }
        .colophon-by { font-family: var(--font-mono); font-size: .66rem; font-weight: 500; letter-spacing: .14em; text-transform: uppercase; color: var(--ink-faint); transition: color .15s ease; }
        .dln-logo { height: 26px; width: auto; display: block; opacity: .92; transition: opacity .15s ease; }
        .colophon-link:hover .dln-logo { opacity: 1; }
        .colophon-link:hover .colophon-by { color: var(--ink-muted); }

        /* ── Reveal on scroll ────────────────────────────────────── */
        [data-reveal] { opacity: 0; transform: translateY(20px); transition: opacity .6s ease, transform .6s ease; }
        [data-reveal].in { opacity: 1; transform: none; }

        /* ── Responsive ──────────────────────────────────────────── */
        @media (max-width: 1100px) { .toolkit { grid-template-columns: 1fr; } }
        @media (max-width: 900px) {
            .nav-links, .nav:not(.nav-auth) .nav-actions { display: none; }
            .nav:not(.nav-auth) .nav-burger { display: inline-flex; }
            .nav[data-open="true"] .nav-mobile { display: block; }
        }
        @media (max-width: 480px) {
            .nav-switch { font-size: .62rem; letter-spacing: .05em; }
        }
        @media (max-width: 820px) {
            .hero-grid { grid-template-columns: 1fr; gap: 52px; }
            .smtp-wrap { max-width: 520px; margin-inline: auto; width: 100%; }
            .term-grid { grid-template-columns: 1fr; gap: 40px; }
            .footer-inner { flex-direction: column; text-align: center; }
            .footer p { text-align: center; }
        }
        @media (max-width: 560px) {
            .container { padding-inline: 18px; }
            .hero-cta .btn, .cta-btns .btn { width: 100%; }
            .hero-cta, .cta-btns { gap: 10px; }
            .announce-text { display: none; }
            .ledger-row { font-size: .74rem; }
            .smtp-body { font-size: .74rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { animation-duration: .001ms !important; animation-iteration-count: 1 !important; transition-duration: .001ms !important; }
            [data-reveal] { opacity: 1 !important; transform: none !important; }
            .smtp-cursor { animation: none !important; }
        }

        /* ═══════════════════════════════════════════════════════════
           AUTH PAGES (login / register) — split-screen, shared tokens
           ═══════════════════════════════════════════════════════════ */
        .nav-switch { font-family: var(--font-mono); font-size: .72rem; font-weight: 500; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); }
        .nav-switch a { color: var(--accent); border-bottom: 1px solid rgba(14,124,102,.3); padding-bottom: 1px; }
        .nav-switch a:hover { border-color: var(--accent); }

        .auth-split { display: grid; grid-template-columns: 55fr 45fr; }
        @media (min-width: 901px) { .auth-split { min-height: calc(100dvh - 150px); } }
        .auth-form-col { display: flex; align-items: center; justify-content: center; padding: clamp(32px, 6vw, 72px) 24px; }
        .auth-form { width: 100%; max-width: 420px; opacity: 0; transform: translateY(12px); animation: authRise .3s ease-out .05s forwards; }
        @keyframes authRise { to { opacity: 1; transform: none; } }

        .auth-head h1 { font-family: var(--font-display); font-weight: 600; font-size: clamp(1.9rem, 4vw, 2.4rem); letter-spacing: -.02em; line-height: 1.04; color: var(--text); }
        .auth-head p { margin-top: 10px; color: var(--muted); font-size: .95rem; }

        .form { margin-top: 28px; display: flex; flex-direction: column; gap: 18px; }
        .field-label-row { display: flex; align-items: baseline; justify-content: space-between; gap: 10px; margin-bottom: 8px; }
        .field label { font-family: var(--font-mono); font-size: .68rem; font-weight: 500; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); transition: color .15s ease; }
        .field:focus-within label { color: var(--accent); }
        .field.has-error label { color: var(--amber); }
        .field-forgot { font-family: var(--font-mono); font-size: .64rem; letter-spacing: .08em; text-transform: uppercase; color: var(--faint); }
        .field-forgot:hover { color: var(--accent); }

        .input-wrap { position: relative; }
        .input { width: 100%; height: 48px; padding: 0 14px; background: var(--surface); border: 1px solid var(--line); border-radius: var(--r-card); font-family: var(--font-sans); font-size: 15px; color: var(--text); transition: border-color .15s ease, box-shadow .15s ease; }
        .input::placeholder { color: #ABAEB5; }
        .input:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(14,124,102,.12); }
        .input.has-toggle { padding-right: 46px; }
        .field.has-error .input { border-color: var(--amber); }
        .field.has-error .input:focus { box-shadow: 0 0 0 3px rgba(180,83,9,.12); }
        /* Kill the blue autofill background */
        .input:-webkit-autofill, .input:-webkit-autofill:hover, .input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text);
            -webkit-box-shadow: 0 0 0 1000px var(--surface) inset;
            box-shadow: 0 0 0 1000px var(--surface) inset;
            transition: background-color 9999s ease-in-out 0s;
        }
        .pw-toggle { position: absolute; top: 0; right: 0; height: 48px; width: 46px; display: inline-flex; align-items: center; justify-content: center; border: 0; background: transparent; color: var(--faint); cursor: pointer; }
        .pw-toggle:hover { color: var(--muted); }
        .pw-toggle:focus-visible { outline: 2px solid var(--accent); outline-offset: -2px; border-radius: 8px; }
        .pw-toggle .lucide { width: 18px; height: 18px; }
        .pw-toggle .i-hide { display: none; }
        .pw-toggle[data-visible="true"] .i-show { display: none; }
        .pw-toggle[data-visible="true"] .i-hide { display: inline-flex; }
        .confirm-ok { position: absolute; top: 0; right: 46px; height: 48px; display: none; align-items: center; color: var(--accent); }
        .confirm-ok.show { display: inline-flex; }
        .confirm-ok .lucide { width: 17px; height: 17px; }

        .field-error { display: none; align-items: center; gap: 6px; margin-top: 7px; font-family: var(--font-mono); font-size: .7rem; letter-spacing: .01em; color: var(--amber); }
        .field-error.show { display: flex; }
        .field-error .lucide { width: 14px; height: 14px; flex: none; }

        @keyframes shake { 10%,90%{transform:translateX(-1px)} 20%,80%{transform:translateX(2px)} 30%,50%,70%{transform:translateX(-4px)} 40%,60%{transform:translateX(4px)} }
        .field.shake .input-wrap { animation: shake .4s ease; }

        .pw-meter { margin-top: 10px; }
        .pw-meter-bar { display: flex; gap: 6px; }
        .pw-meter-seg { height: 4px; flex: 1; border-radius: 999px; background: #E7E5DF; transition: background-color .25s ease; }
        .pw-meter[data-score="1"] .pw-meter-seg:nth-child(1) { background: var(--amber); }
        .pw-meter[data-score="2"] .pw-meter-seg:nth-child(-n+2) { background: #C08329; }
        .pw-meter[data-score="3"] .pw-meter-seg { background: var(--accent); }
        .pw-meter-cap { margin-top: 6px; font-family: var(--font-mono); font-size: .64rem; letter-spacing: .1em; text-transform: uppercase; color: var(--faint); }

        .check { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-family: var(--font-mono); font-size: .7rem; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); user-select: none; }
        .check input { position: absolute; opacity: 0; width: 0; height: 0; }
        .check .box { width: 18px; height: 18px; border: 1px solid var(--line); border-radius: 5px; background: var(--surface); display: inline-flex; align-items: center; justify-content: center; color: #fff; transition: background-color .15s ease, border-color .15s ease; }
        .check .box .lucide { width: 12px; height: 12px; opacity: 0; }
        .check input:checked + .box { background: var(--accent); border-color: var(--accent); }
        .check input:checked + .box .lucide { opacity: 1; }
        .check input:focus-visible + .box { outline: 2px solid var(--accent); outline-offset: 2px; }

        .btn-block { width: 100%; }
        .auth-submit { height: 48px; }
        .auth-submit .spinner { width: 15px; height: 15px; }
        .label-loading:not([hidden]) { display: inline-flex; align-items: center; gap: 9px; }

        .alert { display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border: 1px solid rgba(180,83,9,.3); background: rgba(180,83,9,.07); border-radius: var(--r-card); margin-bottom: 20px; font-family: var(--font-mono); font-size: .74rem; line-height: 1.5; color: #8A4308; }
        .alert .lucide { width: 16px; height: 16px; color: var(--amber); flex: none; margin-top: 1px; }
        .alert-ok { border-color: rgba(14,124,102,.28); background: var(--accent-soft); color: var(--accent); }
        .alert-ok .lucide { color: var(--accent); }

        .auth-success .big-check { width: 56px; height: 56px; border-radius: 50%; background: var(--accent-soft); color: var(--accent); display: inline-flex; align-items: center; justify-content: center; }
        .auth-success .big-check .lucide { width: 30px; height: 30px; }
        .auth-success h1 { margin-top: 22px; font-family: var(--font-display); font-weight: 600; font-size: clamp(1.8rem, 3.6vw, 2.3rem); letter-spacing: -.02em; color: var(--text); }
        .auth-success .line { margin-top: 12px; font-family: var(--font-mono); font-size: .78rem; letter-spacing: .01em; color: var(--muted); }
        .auth-success .line .c { color: var(--accent); }

        /* Dark brand panel */
        .auth-panel { position: relative; overflow: hidden; background: var(--ink); }
        .auth-panel .pc { position: absolute; font-family: var(--font-mono); font-weight: 600; font-size: 3rem; color: rgba(151,162,176,.14); letter-spacing: -.02em; }
        .auth-panel .pc1 { top: 7%; right: 8%; }
        .auth-panel .pc2 { top: 46%; left: 6%; }
        .auth-panel .pc3 { bottom: 9%; right: 14%; }
        .auth-panel-inner { position: relative; z-index: 1; height: 100%; display: flex; flex-direction: column; justify-content: center; padding: 56px; }
        .auth-tr-mask { overflow: hidden; max-height: 320px; -webkit-mask-image: linear-gradient(to bottom, transparent, #000 26%, #000 82%, transparent); mask-image: linear-gradient(to bottom, transparent, #000 26%, #000 82%, transparent); }
        .auth-tr-track { transition: transform .6s ease; font-family: var(--font-mono); font-size: .82rem; line-height: 2.1; color: var(--ink-muted); }
        .auth-tr-track .ln { opacity: .5; white-space: nowrap; }
        .auth-tr-track .cr { color: var(--accent-bright); opacity: .85; }
        .auth-panel-note { position: absolute; left: 56px; bottom: 44px; z-index: 2; font-family: var(--font-mono); font-size: .72rem; letter-spacing: .01em; color: var(--ink-faint); }
        .auth-panel-note .c { color: var(--accent-bright); }
        @media (max-width: 900px) {
            .auth-panel { display: none; }
            .auth-split { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) { .auth-form-col { padding: 40px 20px 56px; } }

        @media (prefers-reduced-motion: reduce) {
            .auth-form { opacity: 1 !important; transform: none !important; animation: none !important; }
            .field.shake .input-wrap { animation: none !important; }
            .auth-tr-track { transition: none !important; }
        }
    </style>
