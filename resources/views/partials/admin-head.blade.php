<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    /* ── Admin design system (site tokens; distinct class names so the app's
          Tailwind .card/.btn on other admin pages are untouched) ───────── */
    :root {
        --ink:#0B1220; --paper:#FAFAF8; --surface:#FFFFFF; --line:#E6E4DE;
        --accent:#0E7C66; --accent-hover:#0B6553; --accent-soft:#E7F3F0; --amber:#B45309;
        --text:#0B1220; --muted:#56606E; --faint:#8A8F98;
        --ink-line:rgba(255,255,255,.10); --ink-muted:#97A2B0; --ink-faint:#6A7482; --accent-bright:#35B896;
        --r-card:10px; --r-btn:8px; --r-badge:6px;
        --f-sans:'Inter',system-ui,-apple-system,'Segoe UI',Roboto,sans-serif;
        --f-disp:'Bricolage Grotesque','Inter',sans-serif;
        --f-mono:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
        --side-w:246px; --rail-w:66px;
    }
    body { background: var(--paper); color: var(--text); font-family: var(--f-sans); -webkit-font-smoothing: antialiased; }
    html, body { overflow-x: clip; }
    .mono { font-family: var(--f-mono); }
    ::selection { background: var(--accent); color: var(--paper); }
    .admin a:focus-visible, .admin button:focus-visible, .admin [tabindex]:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; border-radius: 6px; }
    .admin .lucide { stroke-width: 1.5px; }

    /* ── Shell ─────────────────────────────────────────────────────── */
    .admin { display: flex; min-height: 100vh; }
    .side { width: var(--side-w); flex: none; background: var(--paper); border-right: 1px solid var(--line); display: flex; flex-direction: column; transition: width .18s ease; }
    .side-brand { display: flex; align-items: center; gap: 10px; height: 60px; padding: 0 18px; border-bottom: 1px solid var(--line); }
    .side-brand img { width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--line); flex: none; }
    .side-brand b { font-family: var(--f-mono); font-weight: 600; font-size: .86rem; letter-spacing: .02em; white-space: nowrap; }
    .side-brand .tag { font-family: var(--f-mono); font-size: .58rem; letter-spacing: .16em; text-transform: uppercase; color: var(--accent); border: 1px solid rgba(14,124,102,.24); border-radius: 4px; padding: 1px 5px; }
    .side-nav { flex: 1; overflow-y: auto; padding: 14px 12px; }
    .side-section-label { font-family: var(--f-mono); font-size: .58rem; font-weight: 500; letter-spacing: .16em; text-transform: uppercase; color: var(--faint); padding: 0 10px; margin: 18px 0 8px; }
    .side-section:first-child .side-section-label { margin-top: 4px; }
    .side-item { position: relative; display: flex; align-items: center; gap: 11px; padding: 9px 10px; border-radius: 8px; color: var(--muted); font-size: .9rem; font-weight: 500; transition: background-color .12s ease, color .12s ease; margin-bottom: 2px; }
    .side-item:hover { background: rgba(11,18,32,.04); color: var(--text); }
    .side-item.active { background: var(--ink); color: #fff; }
    .side-item .lucide { width: 18px; height: 18px; flex: none; }
    .side-item .label { white-space: nowrap; overflow: hidden; }
    .side-item .side-badge { margin-left: auto; font-family: var(--f-mono); font-size: .66rem; font-weight: 600; background: var(--accent); color: #fff; border-radius: 999px; min-width: 18px; height: 18px; padding: 0 5px; display: inline-flex; align-items: center; justify-content: center; }
    .side-item.active .side-badge { background: #fff; color: var(--ink); }
    .side-foot { border-top: 1px solid var(--line); padding: 10px 12px; }
    .side-foot .side-item { color: var(--faint); }

    /* Collapsed rail */
    .admin.is-collapsed .side { width: var(--rail-w); }
    .admin.is-collapsed .side-brand b, .admin.is-collapsed .side-brand .tag,
    .admin.is-collapsed .side-item .label, .admin.is-collapsed .side-item .side-badge,
    .admin.is-collapsed .side-section-label { display: none; }
    .admin.is-collapsed .side-brand { justify-content: center; padding: 0; }
    .admin.is-collapsed .side-item { justify-content: center; padding: 9px 0; }
    .admin.is-collapsed .side-section-label { height: 10px; margin: 12px 0 4px; }
    .admin.is-collapsed .side-item:hover::after {
        content: attr(data-tip); position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
        background: var(--ink); color: #fff; font-family: var(--f-mono); font-size: .68rem; letter-spacing: .06em; text-transform: uppercase;
        padding: 6px 9px; border-radius: 6px; white-space: nowrap; z-index: 80; pointer-events: none;
    }

    .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }
    .topbar { position: sticky; top: 0; z-index: 40; display: flex; align-items: center; justify-content: space-between; gap: 16px; height: 60px; padding: 0 20px; background: rgba(250,250,248,.9); backdrop-filter: saturate(150%) blur(8px); border-bottom: 1px solid var(--line); }
    .topbar-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .topbar h1 { font-family: var(--f-disp); font-weight: 600; font-size: 1.3rem; letter-spacing: -.02em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .icon-btn { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); color: var(--muted); cursor: pointer; position: relative; flex: none; }
    .icon-btn:hover { color: var(--text); border-color: #D6D3CA; }
    .icon-btn .lucide { width: 18px; height: 18px; }
    .topbar-right { display: flex; align-items: center; gap: 10px; }
    .smtp-pill { display: inline-flex; align-items: center; gap: 7px; font-family: var(--f-mono); font-size: .68rem; font-weight: 500; letter-spacing: .06em; text-transform: uppercase; padding: 6px 11px; border-radius: 999px; background: var(--accent-soft); color: var(--accent); border: 1px solid rgba(14,124,102,.18); }
    .smtp-pill.bad { background: rgba(180,83,9,.08); color: var(--amber); border-color: rgba(180,83,9,.24); }
    .smtp-pill .d { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .bell-badge { position: absolute; top: -5px; right: -5px; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 999px; background: var(--amber); color: #fff; font-family: var(--f-mono); font-size: .6rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; border: 1.5px solid var(--paper); }
    .avatar-btn { display: inline-flex; align-items: center; gap: 9px; padding: 4px 8px 4px 4px; border: 1px solid var(--line); border-radius: 999px; background: var(--surface); cursor: pointer; }
    .avatar-btn:hover { border-color: #D6D3CA; }
    .avatar { width: 28px; height: 28px; border-radius: 50%; background: var(--ink); color: #fff; font-family: var(--f-mono); font-size: .74rem; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; flex: none; }
    .avatar-name { font-size: .84rem; font-weight: 550; color: var(--text); }
    .dropdown { position: absolute; right: 0; top: calc(100% + 8px); width: 190px; background: var(--surface); border: 1px solid var(--line); border-radius: 10px; padding: 6px; box-shadow: 0 12px 32px -16px rgba(11,18,32,.28); transform-origin: top right; }
    .dropdown a, .dropdown button { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 10px; border-radius: 7px; font-size: .86rem; color: var(--text); background: transparent; border: 0; cursor: pointer; text-align: left; font-family: inherit; }
    .dropdown a:hover, .dropdown button:hover { background: rgba(11,18,32,.05); }
    .dropdown .lucide { width: 16px; height: 16px; color: var(--muted); }
    .dropdown .sep { height: 1px; background: var(--line); margin: 5px 0; }

    .content { padding: 24px 20px 48px; }
    .content-wrap { max-width: 1200px; margin-inline: auto; }

    /* Off-canvas drawer + scrim on small screens */
    .hamburger, .scrim { display: none; }
    @media (max-width: 1000px) {
        .side { position: fixed; inset: 0 auto 0 0; z-index: 70; transform: translateX(-100%); }
        .admin.is-collapsed .side { width: var(--side-w); }
        .admin.is-drawer .side { transform: none; box-shadow: 0 0 60px rgba(11,18,32,.2); }
        .admin.is-collapsed .side-brand b, .admin.is-collapsed .side-item .label,
        .admin.is-collapsed .side-item .side-badge, .admin.is-collapsed .side-section-label,
        .admin.is-collapsed .side-brand .tag { display: revert; }
        .admin.is-collapsed .side-item { justify-content: flex-start; padding: 9px 10px; }
        .scrim { position: fixed; inset: 0; background: rgba(11,18,32,.45); z-index: 65; }
        .admin.is-drawer .scrim { display: block; }
        .hamburger { display: inline-flex; }
        .collapse-btn { display: none; }
    }

    /* ── Stat strip ────────────────────────────────────────────────── */
    .stat-strip { display: grid; grid-template-columns: repeat(4, 1fr); border: 1px solid var(--line); border-radius: var(--r-card); background: var(--surface); overflow: hidden; }
    .stat-seg { display: block; padding: 18px 20px; border-left: 1px solid var(--line); color: inherit; transition: background-color .15s ease; }
    .stat-seg:first-child { border-left: 0; }
    .stat-seg:hover { background: #F6F5F1; }
    .stat-seg .k { font-family: var(--f-mono); font-size: .62rem; font-weight: 500; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
    .stat-seg .v { font-family: var(--f-mono); font-size: 2rem; font-weight: 600; letter-spacing: -.02em; color: var(--text); margin-top: 10px; line-height: 1; }
    .stat-seg .v.amber { color: var(--amber); }
    .stat-seg .sub { font-family: var(--f-mono); font-size: .64rem; letter-spacing: .02em; color: var(--faint); margin-top: 9px; }
    @media (max-width: 820px) {
        .stat-strip { grid-template-columns: 1fr 1fr; }
        .stat-seg:nth-child(odd) { border-left: 0; }
        .stat-seg:nth-child(n+3) { border-top: 1px solid var(--line); }
    }
    @media (max-width: 460px) {
        .stat-strip { grid-template-columns: 1fr; }
        .stat-seg { border-left: 0 !important; border-top: 1px solid var(--line); }
        .stat-seg:first-child { border-top: 0; }
    }

    /* ── Cards / buttons ───────────────────────────────────────────── */
    .acard { border: 1px solid var(--line); border-radius: var(--r-card); background: var(--surface); transition: border-color .15s ease; }
    .acard:hover { border-color: #D9D6CD; }
    .acard-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 15px 18px; border-bottom: 1px solid var(--line); }
    .acard-title { font-family: var(--f-disp); font-weight: 600; font-size: 1.02rem; letter-spacing: -.01em; }
    .acard-sub { font-family: var(--f-mono); font-size: .68rem; letter-spacing: .06em; text-transform: uppercase; color: var(--faint); }
    .acount { font-family: var(--f-mono); font-size: .72rem; font-weight: 600; color: var(--accent); background: var(--accent-soft); padding: 3px 9px; border-radius: 6px; }
    .alink { font-family: var(--f-mono); font-size: .68rem; letter-spacing: .06em; text-transform: uppercase; color: var(--accent); }
    .alink:hover { color: var(--accent-hover); }

    .abtn { display: inline-flex; align-items: center; justify-content: center; gap: 7px; min-height: 38px; padding: 8px 15px; border-radius: var(--r-btn); border: 1px solid transparent; font-family: var(--f-mono); font-size: .72rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; cursor: pointer; white-space: nowrap; transition: background-color .15s, border-color .15s, color .15s; }
    .abtn-accent { background: var(--accent); color: #fff; border-color: var(--accent); }
    .abtn-accent:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    .abtn-ghost { background: transparent; color: var(--muted); border-color: var(--line); }
    .abtn-ghost:hover { color: var(--text); border-color: #D6D3CA; background: #F6F5F1; }
    .abtn-sm { min-height: 32px; padding: 6px 12px; font-size: .68rem; }
    .abtn:disabled { opacity: .55; cursor: default; }

    /* ── Approvals ─────────────────────────────────────────────────── */
    .approval-row { display: flex; align-items: center; gap: 14px; padding: 13px 18px; border-bottom: 1px solid var(--line); overflow: hidden; }
    .approval-row:last-child { border-bottom: 0; }
    .approval-row.removing { height: 0 !important; padding-top: 0 !important; padding-bottom: 0 !important; opacity: 0; border-color: transparent; transition: height .2s ease, padding .2s ease, opacity .2s ease; }
    .av { width: 36px; height: 36px; border-radius: 50%; background: var(--accent-soft); color: var(--accent); font-family: var(--f-mono); font-weight: 600; font-size: .82rem; display: inline-flex; align-items: center; justify-content: center; flex: none; }
    .approval-id { flex: 1; min-width: 0; }
    .approval-id .n { font-weight: 600; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .approval-id .e { font-family: var(--f-mono); font-size: .74rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .approval-time { font-family: var(--f-mono); font-size: .7rem; color: var(--faint); white-space: nowrap; }
    .approval-actions { display: flex; gap: 8px; flex: none; }
    .empty-line { padding: 20px 18px; font-family: var(--f-mono); font-size: .78rem; letter-spacing: .01em; color: var(--faint); }

    /* ── Main grid + activity feed ─────────────────────────────────── */
    .grid-2 { display: grid; grid-template-columns: 60fr 40fr; gap: 18px; align-items: start; }
    @media (max-width: 1000px) { .grid-2 { grid-template-columns: 1fr; } }
    .stack { display: flex; flex-direction: column; gap: 18px; }

    .feed { position: relative; padding: 16px 18px 10px; }
    .feed-rail { position: absolute; left: 24px; top: 22px; bottom: 22px; width: 1px; background: var(--line); }
    .feed-entry { position: relative; padding-left: 26px; }
    .feed-node { position: absolute; left: 2px; top: 15px; width: 9px; height: 9px; border-radius: 50%; background: var(--surface); border: 1.5px solid var(--accent); }
    .feed-entry.type-error .feed-node { border-color: var(--amber); }
    .feed-head { display: flex; align-items: baseline; gap: 11px; padding: 9px 8px 9px 0; border-radius: 8px; }
    .feed-entry.expandable .feed-head { cursor: pointer; }
    .feed-entry.expandable .feed-head:hover { background: #F6F5F1; padding-left: 8px; margin-left: -8px; }
    .feed-time { font-family: var(--f-mono); font-size: .68rem; color: var(--faint); white-space: nowrap; flex: none; width: 58px; }
    .feed-label { font-family: var(--f-mono); font-size: .72rem; font-weight: 600; color: var(--accent); white-space: nowrap; flex: none; }
    .feed-entry.type-error .feed-label { color: var(--amber); }
    .feed-desc { font-size: .85rem; color: var(--muted); min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .feed-desc strong { color: var(--text); font-weight: 600; }
    .feed-chev { margin-left: auto; color: var(--faint); transition: transform .2s ease; flex: none; display: inline-flex; }
    .feed-chev .lucide { width: 15px; height: 15px; }
    .feed-entry.open .feed-chev { transform: rotate(90deg); }
    .feed-receipt { max-height: 0; overflow: hidden; transition: max-height .22s ease; }
    .feed-entry.open .feed-receipt { max-height: 200px; }
    .feed-receipt-inner { margin: 0 0 10px; border: 1px solid var(--line); border-radius: 8px; padding: 10px 13px; background: #FBFBF9; }
    .rline { display: flex; align-items: baseline; font-family: var(--f-mono); font-size: .74rem; padding: 3px 0; }
    .rline .rk { color: var(--muted); white-space: nowrap; }
    .rline .rlead { flex: 1; margin: 0 8px; border-bottom: 1px dotted #C7C4BB; transform: translateY(-4px); }
    .rline .rv { color: var(--text); white-space: nowrap; }
    .rline .rv.amber { color: var(--amber); }

    /* ── SMTP terminal card ────────────────────────────────────────── */
    .smtp-card.failed { border-color: rgba(180,83,9,.5); }
    .smtp-term { margin: 16px 18px; border-radius: 8px; background: var(--ink); border: 1px solid var(--ink-line); padding: 14px 16px; font-family: var(--f-mono); font-size: .76rem; line-height: 1.95; }
    .smtp-term .r { color: var(--ink-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .smtp-term .r .kk { color: var(--ink-faint); }
    .smtp-term .r .vv { color: #DCE2E9; }
    .smtp-term .ok { color: var(--accent-bright); }
    .smtp-term .bad { color: #F0A868; }
    .smtp-term .tl { display: none; align-items: center; gap: 8px; }
    .smtp-term .tl.show { display: flex; }
    .smtp-term .tl .sp { width: 11px; height: 11px; border: 2px solid var(--accent-bright); border-top-color: transparent; border-radius: 50%; display: inline-block; animation: aspin .7s linear infinite; }
    .smtp-term .tl .ck { color: var(--accent-bright); }
    @keyframes aspin { to { transform: rotate(360deg); } }
    .smtp-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 0 18px 16px; }
    .smtp-foot .last { font-family: var(--f-mono); font-size: .7rem; color: var(--faint); }
    .smtp-foot .last.bad { color: var(--amber); }

    /* ── Volume chart ──────────────────────────────────────────────── */
    .vchart { padding: 18px; position: relative; }
    .vbar { fill: var(--accent); transition: fill .12s ease; cursor: pointer; }
    .vbar:hover, .vbar.hot { fill: var(--accent-hover); }
    .vbar-fail { fill: var(--amber); opacity: .85; }
    .vaxis { stroke: var(--line); }
    .vlabel { font-family: var(--f-mono); font-size: 9px; fill: var(--faint); }
    .vtip { position: absolute; z-index: 5; pointer-events: none; background: var(--ink); color: #fff; font-family: var(--f-mono); font-size: .68rem; padding: 6px 9px; border-radius: 6px; white-space: nowrap; opacity: 0; transform: translate(-50%, -6px); transition: opacity .1s ease; }
    .vtip .ok { color: var(--accent-bright); } .vtip .bad { color: #F0A868; }

    /* ── Toasts ────────────────────────────────────────────────────── */
    .toasts { position: fixed; right: 20px; bottom: 20px; z-index: 90; display: flex; flex-direction: column; gap: 10px; align-items: flex-end; }
    .toast { display: flex; align-items: center; gap: 9px; background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 10px 13px; font-family: var(--f-mono); font-size: .73rem; color: var(--text); box-shadow: 0 10px 28px -16px rgba(11,18,32,.3); animation: toastIn .2s ease; max-width: 340px; }
    .toast .lucide { width: 15px; height: 15px; color: var(--accent); flex: none; }
    .toast.err { border-color: rgba(180,83,9,.3); }
    .toast.err .lucide { color: var(--amber); }
    @keyframes toastIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }

    /* ── Pagination (design-system, used app-wide) ─────────────────── */
    .pager { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
    .pager-info { font-family: var(--f-mono); font-size: .68rem; letter-spacing: .04em; color: var(--faint); }
    .pager-links { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
    .pager-btn, .pager-num { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 11px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); font-family: var(--f-mono); font-size: .72rem; font-weight: 500; color: var(--muted); transition: border-color .12s ease, color .12s ease, background-color .12s ease; }
    .pager-btn:hover, .pager-num:hover { border-color: #D6D3CA; color: var(--text); background: #F6F5F1; }
    .pager-btn:focus-visible, .pager-num:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
    .pager-num.is-active { background: var(--ink); border-color: var(--ink); color: #fff; }
    .pager-btn.is-disabled { opacity: .45; pointer-events: none; }
    .pager-gap { padding: 0 2px; color: var(--faint); font-family: var(--f-mono); font-size: .72rem; }

    /* ── Page enter + empty states ─────────────────────────────────── */
    .enter { opacity: 0; transform: translateY(8px); animation: enterUp .25s ease-out forwards; }
    @keyframes enterUp { to { opacity: 1; transform: none; } }
    .empty-block { padding: 40px 18px; text-align: center; }
    .empty-block p { font-family: var(--f-mono); font-size: .8rem; color: var(--faint); }
    .empty-block .abtn { margin-top: 16px; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        .enter { opacity: 1 !important; transform: none !important; }
        .side { transition: none !important; }
    }
</style>
