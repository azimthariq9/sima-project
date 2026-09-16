@extends('layouts.sima')

@section('page_title',    'Broadcast Notification')
@section('page_section',  'NOTIFIKASI')
@section('page_subtitle', 'Send notifications to students')

@section('main_content')

@if(session('success'))
<div class="sima-alert sima-alert--success mb-4" style="padding:12px 16px;background:rgba(5,150,105,.1);border:1px solid var(--c-green);border-radius:8px;color:var(--c-green);font-size:13px;">
    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
</div>
@endif

<div class="row g-4">

    {{-- ── FORM KIRIM ──────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="sima-card" style="position:sticky;top:20px;">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Send Notification</h5>
            </div>
            <div style="padding:20px 24px;">
                <form method="POST" action="{{ route('kln.broadcast.send') }}" id="broadcastForm">
                    @csrf

                    {{-- Subject --}}
                    <div class="mb-3">
                        <label class="sima-label">Notification Title <span style="color:var(--c-red)">*</span></label>
                        <input type="text" name="subject" class="sima-input @error('subject') is-invalid @enderror"
                               value="{{ old('subject') }}" placeholder="e.g., National Holiday Announcement" required>
                        @error('subject')<div class="invalid-feedback" style="color:var(--c-red);font-size:12px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Message --}}
                    <div class="mb-3">
                        <label class="sima-label">Notification Message <span style="color:var(--c-red)">*</span></label>
                        <textarea name="message" rows="5" class="sima-input @error('message') is-invalid @enderror"
                                  placeholder="Tulis isi notifikasi di sini..." required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback" style="color:var(--c-red);font-size:12px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Target --}}
                    <div class="mb-3">
                        <label class="sima-label">Send To</label>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                                <input type="radio" name="target" value="all"
                                       {{ old('target','all') === 'all' ? 'checked' : '' }}
                                       onchange="toggleMahasiswaPicker(this.value)">
                                All Students
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                                <input type="radio" name="target" value="selected"
                                       {{ old('target') === 'selected' ? 'checked' : '' }}
                                       onchange="toggleMahasiswaPicker(this.value)">
                                Select Students
                            </label>
                        </div>
                    </div>

                    {{-- Mahasiswa picker (hidden by default) --}}
                    <div id="mahasiswaPicker" style="display:{{ old('target') === 'selected' ? 'block' : 'none' }};margin-bottom:16px;">
                        <label class="sima-label">Select Students</label>

                        {{-- Filter bar --}}
                        <div style="display:flex;gap:6px;margin-bottom:6px;flex-wrap:wrap;">
                            <input type="text" id="mhsSearch" class="sima-input sima-input--sm"
                                   placeholder="Search name / NPM..." oninput="filterMhsPicker()"
                                   style="flex:1;min-width:120px;">
                            <select id="mhsJurusan" class="sima-input sima-input--sm" onchange="filterMhsPicker()"
                                    style="flex:1;min-width:120px;">
                                <option value="">All Majors</option>
                                @foreach($jurusanList as $j)
                                    <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                                @endforeach
                            </select>
                            <select id="mhsKelas" class="sima-input sima-input--sm" onchange="filterMhsPicker()"
                                    style="flex:1;min-width:110px;">
                                <option value="">All Classes</option>
                                @foreach($kelasList as $k)
                                    <option value="{{ $k->id }}">{{ $k->kodeKelas }}{{ $k->tahunAjar ? ' ('.$k->tahunAjar.')' : '' }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="resetMhsFilter()" class="sima-btn sima-btn--outline sima-btn--sm" title="Reset filter">
                                <i class="fas fa-redo"></i>
                            </button>
                        </div>

                        <div id="mhsPickerList" style="border:1px solid var(--c-border);border-radius:8px;max-height:220px;overflow-y:auto;padding:8px;">
                            @forelse($mahasiswaList as $mhs)
                            <label class="mhs-item" style="display:flex;align-items:center;gap:8px;padding:5px 6px;cursor:pointer;font-size:13px;border-radius:5px;"
                                   data-nama="{{ strtolower($mhs->nama . ' ' . $mhs->npm) }}"
                                   data-jurusan="{{ $mhs->jurusan_id ?? '' }}"
                                   data-kelas="{{ $mahasiswaKelasMap[$mhs->id] ?? '' }}"
                                   onmouseover="this.style.background='var(--c-bg-2)'"
                                   onmouseout="this.style.background='transparent'">
                                <input type="checkbox" name="mahasiswa_ids[]" value="{{ $mhs->id }}"
                                       {{ in_array($mhs->id, old('mahasiswa_ids', [])) ? 'checked' : '' }}>
                                <span style="min-width:0;">
                                    <span style="font-weight:600;">{{ $mhs->nama }}</span>
                                    <span style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3);margin-left:6px;">{{ $mhs->npm }}</span>
                                    @if($mhs->namaJurusan)
                                    <span style="font-size:11px;color:var(--c-text-3);margin-left:4px;">· {{ $mhs->namaJurusan }}</span>
                                    @endif
                                </span>
                            </label>
                            @empty
                            <div style="padding:10px;font-size:13px;color:var(--c-text-3);">No active students.</div>
                            @endforelse
                            <div id="mhsEmpty" style="display:none;padding:10px;font-size:13px;color:var(--c-text-3);text-align:center;">
                                No matching students.
                            </div>
                        </div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:4px;" id="mhsCount">
                            {{ $mahasiswaList->count() }} mahasiswa aktif
                        </div>
                        @error('mahasiswa_ids')<div style="color:var(--c-red);font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="sima-btn sima-btn--full">
                        <i class="fas fa-paper-plane me-2"></i> Send Notification
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── RIWAYAT TERKIRIM ─────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Sent History</h5>
                <span class="sima-badge sima-badge--blue">{{ $riwayat->count() }}</span>
            </div>

            @forelse($riwayat as $notif)
            @php
                $typeMap = [
                    'broadcast'    => ['label' => 'Broadcast',    'cls' => 'sima-badge--blue',   'ic' => 'fa-bullhorn'],
                    'document'     => ['label' => 'Dokumen',      'cls' => 'sima-badge--green',  'ic' => 'fa-file-alt'],
                    'account'      => ['label' => 'Akun',         'cls' => 'sima-badge--purple', 'ic' => 'fa-user-check'],
                    'announcement' => ['label' => 'Announcement',   'cls' => 'sima-badge--amber',  'ic' => 'fa-bell'],
                ];
                $tm = $typeMap[$notif->type] ?? ['label' => $notif->type, 'cls' => '', 'ic' => 'fa-bell'];
                $readPct = $notif->total_penerima > 0
                    ? round($notif->total_dibaca / $notif->total_penerima * 100)
                    : 0;
            @endphp
            <div style="padding:16px 24px;border-bottom:1px solid var(--c-border);">
                <div style="display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px;">
                            <span class="sima-badge {{ $tm['cls'] }}">
                                <i class="fas {{ $tm['ic'] }} me-1"></i>{{ $tm['label'] }}
                            </span>
                            <span style="font-size:14px;font-weight:600;">{{ $notif->subject }}</span>
                        </div>
                        <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px;">
                            {{ \Carbon\Carbon::parse($notif->created_at)->isoFormat('D MMM YYYY, HH:mm') }}
                        </div>
                        <div style="font-size:13px;color:var(--c-text-2);line-height:1.5;">
                            {{ \Illuminate\Support\Str::limit($notif->message, 120) }}
                        </div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;min-width:90px;">
                        <div style="font-size:12px;color:var(--c-text-3);">Recipients</div>
                        <div style="font-size:18px;font-weight:700;line-height:1.2;">{{ $notif->total_penerima }}</div>
                        <div style="font-size:11px;color:var(--c-text-3);margin-top:2px;">
                            {{ $notif->total_dibaca }} read ({{ $readPct }}%)
                        </div>
                        {{-- mini progress bar --}}
                        <div style="width:80px;height:3px;background:var(--c-border);border-radius:999px;overflow:hidden;margin-top:5px;margin-left:auto;">
                            <div style="height:100%;border-radius:999px;width:{{ $readPct }}%;background:var(--c-accent);"></div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5">
                <i class="fas fa-paper-plane fa-2x d-block mb-2" style="opacity:.3;"></i>
                No notifications sent yet.
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@push('page_js')
<script>
function toggleMahasiswaPicker(val) {
    document.getElementById('mahasiswaPicker').style.display = val === 'selected' ? 'block' : 'none';
}

function filterMhsPicker() {
    const q       = document.getElementById('mhsSearch').value.toLowerCase();
    const jurusan = document.getElementById('mhsJurusan').value;
    const kelas   = document.getElementById('mhsKelas').value;
    const items   = document.querySelectorAll('#mhsPickerList .mhs-item');
    let visible   = 0;

    items.forEach(el => {
        const matchNama    = !q       || el.dataset.nama.includes(q);
        const matchJurusan = !jurusan || el.dataset.jurusan === jurusan;
        const matchKelas   = !kelas   || (el.dataset.kelas && el.dataset.kelas.split(',').includes(kelas));
        const show         = matchNama && matchJurusan && matchKelas;
        el.style.display   = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('mhsEmpty').style.display = visible === 0 ? '' : 'none';
    document.getElementById('mhsCount').textContent   = visible + ' mahasiswa ditampilkan';
}

function resetMhsFilter() {
    document.getElementById('mhsSearch').value   = '';
    document.getElementById('mhsJurusan').value  = '';
    document.getElementById('mhsKelas').value    = '';
    filterMhsPicker();
}
</script>
@endpush
