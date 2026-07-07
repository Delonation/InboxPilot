<x-layouts.dashboard title="Import contacts">
    <div style="max-width:760px; margin-inline:auto;">
        <header style="margin-bottom:20px;">
            <h1 style="font-family:var(--f-disp); font-weight:600; font-size:clamp(1.6rem,3vw,2rem); letter-spacing:-.02em;">Import contacts</h1>
            <p style="margin-top:6px; color:var(--muted); font-size:.92rem;">Upload a CSV file. The <span class="mono" style="color:var(--text);">email</span> column is required.</p>
        </header>

        <form method="POST" action="{{ route('contacts.import.store') }}" enctype="multipart/form-data" id="import-form">
            @csrf
            <div class="dropzone" id="dropzone" role="button" tabindex="0" aria-label="Choose or drop a CSV file">
                <input id="file" name="file" type="file" accept=".csv,text/csv" required hidden>
                <div class="dz-idle">
                    <span class="dz-icon"><x-lucide name="upload" class="lucide" /></span>
                    <p class="dz-title">Drag &amp; drop your CSV here, or <span class="dz-browse">browse</span></p>
                    <p class="dz-hint">Max {{ round(config('inboxpilot.csv.max_kb') / 1024, 1) }} MB · columns: email (required), first_name, last_name, phone, company, tags</p>
                </div>
                <div class="dz-chosen" hidden>
                    <span class="dz-file-icon"><x-lucide name="file-text" class="lucide" /></span>
                    <div style="min-width:0;">
                        <div class="dz-file-name mono"></div>
                        <div class="dz-file-size mono"></div>
                    </div>
                    <button type="button" class="dz-clear" aria-label="Remove file"><x-lucide name="x" class="lucide" /></button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('file')" class="mt-2" />

            <div class="csv-example">
                <div class="csv-example-head">
                    <span class="csv-label mono">Example header</span>
                    <button type="button" class="abtn abtn-ghost abtn-sm" id="download-sample">
                        <x-lucide name="download" class="lucide" /> Sample CSV
                    </button>
                </div>
                <code class="mono">email,first_name,last_name,company,tags</code>
            </div>

            <div style="display:flex; justify-content:flex-end; margin-top:18px;">
                <button type="submit" class="abtn abtn-accent" id="import-submit" disabled>Upload and import</button>
            </div>
        </form>

        @if ($recent->count())
            <div style="margin-top:34px;">
                <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:14px;">
                    <h2 style="font-family:var(--f-disp); font-weight:600; font-size:1.15rem; letter-spacing:-.01em;">Recent imports</h2>
                    <span class="mono" style="font-size:.66rem; letter-spacing:.1em; text-transform:uppercase; color:var(--faint);">Last {{ $recent->count() }}</span>
                </div>
                <div class="import-list">
                    @foreach ($recent as $imp)
                        <div class="import-card">
                            <div class="import-top">
                                <div class="import-file">
                                    <x-lucide name="file-text" class="lucide" />
                                    <span class="mono">{{ $imp->filename }}</span>
                                </div>
                                <span class="import-when mono">{{ $imp->created_at->diffForHumans(['short' => true]) }}</span>
                            </div>
                            <div class="import-stats">
                                <div class="istat"><span class="iv">{{ number_format($imp->total_rows) }}</span><span class="ik">rows</span></div>
                                <div class="istat ok"><span class="iv">{{ number_format($imp->imported) }}</span><span class="ik">imported</span></div>
                                <div class="istat"><span class="iv">{{ number_format($imp->skipped_duplicates) }}</span><span class="ik">skipped</span></div>
                                <div class="istat {{ $imp->invalid_emails > 0 ? 'amber' : '' }}"><span class="iv">{{ number_format($imp->invalid_emails) }}</span><span class="ik">invalid</span></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .dropzone { position: relative; display: block; border: 1.5px dashed #CFC9BC; border-radius: 12px; background: #FBFBF9; padding: 38px 24px; text-align: center; cursor: pointer; transition: border-color .15s ease, background-color .15s ease; }
        .dropzone:hover { border-color: var(--accent); background: #F6F9F8; }
        .dropzone:focus-visible { outline: 2px solid var(--accent); outline-offset: 3px; }
        .dropzone.dragover { border-color: var(--accent); background: var(--accent-soft); }
        .dz-icon { width: 46px; height: 46px; border-radius: 11px; background: var(--accent-soft); color: var(--accent); display: inline-flex; align-items: center; justify-content: center; }
        .dz-icon .lucide { width: 22px; height: 22px; }
        .dz-title { margin-top: 15px; font-size: .95rem; color: var(--text); }
        .dz-browse { color: var(--accent); font-weight: 600; text-decoration: underline; text-underline-offset: 2px; }
        .dz-hint { margin-top: 9px; font-family: var(--f-mono); font-size: .66rem; letter-spacing: .01em; color: var(--faint); }
        .dz-chosen:not([hidden]) { display: flex; align-items: center; gap: 13px; text-align: left; }
        .dz-file-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--accent-soft); color: var(--accent); display: inline-flex; align-items: center; justify-content: center; flex: none; }
        .dz-file-icon .lucide { width: 19px; height: 19px; }
        .dz-file-name { font-size: .86rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .dz-file-size { font-size: .7rem; color: var(--faint); margin-top: 3px; }
        .dz-clear { margin-left: auto; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border: 1px solid var(--line); border-radius: 8px; background: var(--surface); color: var(--muted); cursor: pointer; flex: none; }
        .dz-clear:hover { color: var(--text); border-color: #D6D3CA; }
        .dz-clear .lucide { width: 15px; height: 15px; }

        .csv-example { margin-top: 16px; border: 1px solid var(--line); border-radius: 10px; background: var(--surface); padding: 12px 14px; }
        .csv-example-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .csv-example .csv-label { font-size: .58rem; font-weight: 500; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); }
        #download-sample .lucide { width: 14px; height: 14px; }
        .csv-example code { display: block; margin-top: 8px; font-size: .8rem; color: var(--text); }

        .import-list { display: flex; flex-direction: column; gap: 10px; }
        .import-card { border: 1px solid var(--line); border-radius: 10px; background: var(--surface); padding: 14px 16px; transition: border-color .15s ease; }
        .import-card:hover { border-color: #D9D6CD; }
        .import-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .import-file { display: flex; align-items: center; gap: 9px; min-width: 0; }
        .import-file .lucide { width: 16px; height: 16px; color: var(--muted); flex: none; }
        .import-file .mono { font-size: .82rem; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .import-when { font-size: .68rem; color: var(--faint); white-space: nowrap; flex: none; }
        .import-stats { display: flex; gap: 8px; margin-top: 12px; }
        .istat { flex: 1; min-width: 0; border: 1px solid var(--line); border-radius: 8px; padding: 8px 10px; }
        .istat .iv { display: block; font-family: var(--f-mono); font-size: 1.05rem; font-weight: 600; color: var(--text); line-height: 1; }
        .istat .ik { display: block; font-family: var(--f-mono); font-size: .58rem; letter-spacing: .1em; text-transform: uppercase; color: var(--faint); margin-top: 5px; }
        .istat.ok .iv { color: var(--accent); }
        .istat.amber .iv { color: var(--amber); }
    </style>

    @push('scripts')
        <script>
            (function () {
                const dz = document.getElementById('dropzone');
                if (!dz) return;
                const input = document.getElementById('file');
                const submit = document.getElementById('import-submit');
                const idle = dz.querySelector('.dz-idle');
                const chosen = dz.querySelector('.dz-chosen');
                const nameEl = dz.querySelector('.dz-file-name');
                const sizeEl = dz.querySelector('.dz-file-size');
                const clearBtn = dz.querySelector('.dz-clear');

                const human = (b) => b < 1024 ? b + ' B' : b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(1) + ' MB';

                function showFile(file) {
                    idle.hidden = true; chosen.hidden = false;
                    nameEl.textContent = file.name; sizeEl.textContent = human(file.size);
                    submit.disabled = false;
                }
                function clearFile() {
                    input.value = ''; idle.hidden = false; chosen.hidden = true; submit.disabled = true;
                }

                dz.addEventListener('click', (e) => { if (!e.target.closest('.dz-clear')) input.click(); });
                dz.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } });
                input.addEventListener('change', () => { input.files[0] ? showFile(input.files[0]) : clearFile(); });
                clearBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); clearFile(); });

                ['dragenter', 'dragover'].forEach((ev) => dz.addEventListener(ev, (e) => { e.preventDefault(); dz.classList.add('dragover'); }));
                dz.addEventListener('dragleave', (e) => { if (!dz.contains(e.relatedTarget)) dz.classList.remove('dragover'); });
                dz.addEventListener('drop', (e) => {
                    e.preventDefault(); dz.classList.remove('dragover');
                    const f = e.dataTransfer.files[0];
                    if (f) { input.files = e.dataTransfer.files; showFile(f); }
                });

                // Download a ready-to-edit sample CSV
                const sampleBtn = document.getElementById('download-sample');
                if (sampleBtn) {
                    sampleBtn.addEventListener('click', () => {
                        const csv = [
                            'email,first_name,last_name,phone,company,tags',
                            'ava@acme.io,Ava,Chen,+1 555 0100,Acme Inc,vip',
                            'jonas@lumen.co,Jonas,Meyer,,Lumen,newsletter',
                            'mira@nova.dev,Mira,Patel,+1 555 0142,Nova,customer',
                        ].join('\r\n') + '\r\n';
                        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'sample-contacts.csv';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        URL.revokeObjectURL(url);
                    });
                }
            })();
        </script>
    @endpush
</x-layouts.dashboard>
