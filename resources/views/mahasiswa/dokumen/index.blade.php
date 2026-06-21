@extends('layouts.sima')

@section('page_title',   'Dokumen & Request')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Dokumen penting dan permintaan dokumen ke KLN')

@section('main_content')

@php
// Status dokumen berdasarkan tglKdlwrs
$today = now()->toDateString();
$soon  = now()->addDays(30)->toDateString();

$dokStatusInfo = function($dok, $today, $soon) {
    $s = $dok->status ?? 'sedang diproses';
    if ($s === 'sedang diproses') {
        return ['label' => 'Sedang Diproses', 'cls' => 'sima-badge--blue', 'bar' => '#3b82f6'];
    }
    $exp = $dok->tglKdlwrs ?? null;
    if (!$exp) return ['label' => 'Tidak Diketahui', 'cls' => 'sima-badge--grey', 'bar' => '#94a3b8'];
    if ($exp < $today)  return ['label' => 'Kedaluwarsa', 'cls' => 'sima-badge--red',   'bar' => '#ef4444'];
    if ($exp <= $soon)  return ['label' => 'Segera Habis', 'cls' => 'sima-badge--amber', 'bar' => '#f59e0b'];
    return ['label' => 'Valid', 'cls' => 'sima-badge--green', 'bar' => '#22c55e'];
};

// Icon per jenis dokumen
$iconMap = [
    'passport'   => 'fa-passport',
    'paspor'     => 'fa-passport',
    'kitas'      => 'fa-id-card',
    'kitap'      => 'fa-id-card',
    'visa'       => 'fa-plane',
    'asuransi'   => 'fa-heart-pulse',
    'surat'      => 'fa-file-signature',
    'skck'       => 'fa-shield-halved',
    'ijazah'     => 'fa-graduation-cap',
    'transkrip'  => 'fa-scroll',
];
$dokIcon = function($nama, $iconMap) {
    $lower = strtolower($nama ?? '');
    foreach ($iconMap as $key => $icon) {
        if (str_contains($lower, $key)) return $icon;
    }
    return 'fa-file-lines';
};

$rstatus = [
    'approved'   => ['label'=>'Disetujui', 'cls'=>'green'],
    'pending'    => ['label'=>'Menunggu',  'cls'=>'blue'],
    'rejected'   => ['label'=>'Ditolak',   'cls'=>'red'],
    'processing' => ['label'=>'Diproses',  'cls'=>'amber'],
];
@endphp

{{-- ══════════════════════════════════════
     CARD ATAS: DOKUMEN PENTING
══════════════════════════════════════ --}}
<div class="sima-card sima-fade" style="margin-bottom:16px">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Dokumen Penting</h5>
            <div class="sima-card__subtitle">Dokumen resmi yang telah diupload dan diverifikasi KLN</div>
        </div>
        {{-- Upload button — POST ke mahasiswa.dokumen.store --}}
        <button onclick="document.getElementById('modalUpload').style.display='flex'"
                class="sima-btn sima-btn--sm">
            <i class="fas fa-upload"></i> Upload Dokumen
        </button>
    </div>

    <div class="sima-card__body">
        @if($dokumen->isEmpty())
            <div style="text-align:center;padding:40px 20px;color:var(--c-text-3)">
                <i class="fas fa-folder-open" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
                <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">Belum ada dokumen yang diupload</div>
                <div style="font-size:12.5px;margin-top:4px">Upload dokumen penting seperti paspor, KITAS, dan asuransi kesehatan.</div>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px">
                @foreach($dokumen as $dok)
                    @php
                        $stInfo = $dokStatusInfo($dok, $today, $soon);
                        $icon   = $dokIcon($dok->namaDkmn ?? $dok->tipeDkmn, $iconMap);
                        $exp    = $dok->tglKdlwrs
                            ? \Carbon\Carbon::parse($dok->tglKdlwrs)->format('d M Y')
                            : '-';
                        $terbit = $dok->tglTerbit
                            ? \Carbon\Carbon::parse($dok->tglTerbit)->format('d M Y')
                            : '-';
                    @endphp
                    <div style="border:1px solid var(--c-border-soft);border-radius:14px;padding:16px;background:var(--c-surface);transition:box-shadow .15s"
                         onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
                         onmouseout="this.style.boxShadow='none'">

                        {{-- Icon + badge status --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
                            <div style="width:40px;height:40px;border-radius:10px;background:var(--c-bg);display:flex;align-items:center;justify-content:center;color:var(--c-accent);font-size:18px">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <span class="sima-badge {{ $stInfo['cls'] }}" style="font-size:10.5px">
                                {{ $stInfo['label'] }}
                            </span>
                        </div>

                        {{-- Nama dokumen --}}
                        <div style="font-size:13.5px;font-weight:700;color:var(--c-text-1);margin-bottom:2px;line-height:1.3">
                            {{ $dok->namaDkmn ?? str_replace('_', ' ', $dok->tipeDkmn) }}
                        </div>
                        <div style="font-size:11.5px;color:var(--c-text-3);margin-bottom:10px">
                            {{ $dok->penerbit ?? '-' }}
                        </div>

                        {{-- Tanggal --}}
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:11px">
                            <div style="background:var(--c-bg);border-radius:7px;padding:7px 9px">
                                <div style="color:var(--c-text-3);margin-bottom:1px">Terbit</div>
                                <div style="font-weight:600;color:var(--c-text-2)">{{ $terbit }}</div>
                            </div>
                            <div style="background:var(--c-bg);border-radius:7px;padding:7px 9px">
                                <div style="color:var(--c-text-3);margin-bottom:1px">Berlaku s/d</div>
                                <div style="font-weight:600;color:{{ $stInfo['bar'] }}">{{ $exp }}</div>
                            </div>
                        </div>

                        {{-- Download --}}
                        <div style="margin-top:10px">
                            <a href="{{ route('mahasiswa.dokumen.download', $dok->id) }}"
                               class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full"
                               style="font-size:11.5px">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>


{{-- ══════════════════════════════════════
     CARD BAWAH: REQUEST DOKUMEN
══════════════════════════════════════ --}}
<div class="row g-3 sima-fade sima-fade--1">

    {{-- Form request --}}
    <div class="col-md-7">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Request Dokumen ke KLN</h5>
                    <div class="sima-card__subtitle">Ajukan permintaan dokumen resmi</div>
                </div>
            </div>
            <div class="sima-card__body">
                @if($errors->any())
                <div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);border-radius:10px;padding:12px 16px;margin-bottom:16px">
                    @foreach($errors->all() as $error)
                        <div style="font-size:12.5px;color:#b91c1c">· {{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('mahasiswa.request.store') }}">
                    @csrf

                    <div style="margin-bottom:16px">
                        <label class="sima-label">Jenis Dokumen <span style="color:var(--c-red)">*</span></label>
                        <select name="tipeDkmn" class="sima-input" required>
                            <option value="">— Pilih jenis dokumen —</option>
                            @foreach(\App\Enums\TipeDok::cases() as $dok)
                                <option value="{{ $dok->value }}" {{ old('tipeDkmn') == $dok->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', $dok->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:16px">
                        <label class="sima-label">Keperluan / Keterangan <span style="color:var(--c-red)">*</span></label>
                        <textarea name="message" class="sima-input" rows="3"
                                  placeholder="Jelaskan keperluan dokumen ini…"
                                  required style="resize:vertical">{{ old('message') }}</textarea>
                    </div>

                    <div style="display:flex;gap:10px">
                        <button type="submit" class="sima-btn">
                            <i class="fas fa-paper-plane"></i> Kirim Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Riwayat request --}}
    <div class="col-md-5">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Riwayat Request</h5>
                    <div class="sima-card__subtitle">5 request terakhir</div>
                </div>
            </div>
            <div class="sima-card__body" style="padding:0">
                @forelse($recentRequests as $req)
                    @php
                        $r  = is_array($req) ? $req : (array)$req;
                        $rs = $rstatus[$r['status']] ?? $rstatus['pending'];
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--c-border-soft)">
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13px;font-weight:600;color:var(--c-text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $r['name'] }}
                            </div>
                            <div style="font-family:var(--f-mono);font-size:10.5px;color:var(--c-text-3);margin-top:2px">
                                {{ $r['code'] }} · {{ $r['date'] }}
                            </div>
                        </div>
                        <span class="sima-badge sima-badge--{{ $rs['cls'] }}">{{ $rs['label'] }}</span>
                    </div>
                @empty
                    <div style="text-align:center;padding:24px;color:var(--c-text-3);font-size:13px">
                        Belum ada request
                    </div>
                @endforelse
                <div style="padding:12px 16px">
                    <a href="{{ route('mahasiswa.request.index') }}"
                       class="sima-btn sima-btn--outline sima-btn--sm sima-btn--full">
                        <i class="fas fa-list"></i> Lihat Semua Request
                    </a>
                </div>
            </div>
        </div>

        {{-- Info --}}
        <div class="sima-alert sima-alert--blue" style="margin-top:10px">
            <i class="fas fa-info-circle sima-alert__icon"></i>
            <div class="sima-alert__text" style="font-size:12px">
                Request diproses dalam 1–3 hari kerja oleh KLN.
            </div>
        </div>
    </div>

</div>


{{-- ══════════════════════════════════════
     MODAL UPLOAD DOKUMEN
══════════════════════════════════════ --}}
<div id="modalUpload" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:460px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Upload Dokumen Penting</div>
            <button onclick="document.getElementById('modalUpload').style.display='none'"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b;font-size:14px">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form method="POST" action="{{ route('mahasiswa.dokumen.store') }}" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:14px">
                    <label class="sima-label">Jenis Dokumen <span style="color:var(--c-red)">*</span></label>
                    <select name="tipeDkmn" class="sima-input" required>
                        <option value="">— Pilih —</option>
                        @foreach(\App\Enums\TipeDok::cases() as $dok)
                            <option value="{{ $dok->value }}">{{ str_replace('_', ' ', $dok->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Nomor Dokumen <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="noDkmn" class="sima-input" placeholder="cth. A1234567" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                    <div>
                        <label class="sima-label">Tanggal Terbit <span style="color:var(--c-red)">*</span></label>
                        <input type="date" name="tglTerbit" class="sima-input" required>
                    </div>
                    <div>
                        <label class="sima-label">Berlaku s/d <span style="color:var(--c-red)">*</span></label>
                        <input type="date" name="tglKdlwrs" class="sima-input" required>
                    </div>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Penerbit <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="penerbit" class="sima-input" placeholder="cth. Ditjen Imigrasi" required>
                </div>
                <div style="margin-bottom:18px">
                    <label class="sima-label">File Dokumen</label>
                    <input type="file" name="file" class="sima-input" accept=".pdf,.jpg,.jpeg,.png"
                           style="padding:7px 12px;font-size:12.5px">
                    <div style="font-size:11.5px;color:var(--c-text-3);margin-top:4px">PDF, JPG, PNG — maks. 5MB</div>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                    <button type="button" onclick="document.getElementById('modalUpload').style.display='none'"
                            class="sima-btn sima-btn--outline">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.getElementById('modalUpload').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
