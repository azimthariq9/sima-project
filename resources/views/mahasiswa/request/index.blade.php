@extends('layouts.sima')

@section('page_title',   'Semua Request')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Riwayat permintaan dokumen ke KLN')

@section('main_content')

@php
$statusMap = [
    'pending'    => ['label' => 'Menunggu',  'cls' => 'blue',  'ico' => 'fa-clock'],
    'approved'   => ['label' => 'Disetujui', 'cls' => 'green', 'ico' => 'fa-circle-check'],
    'rejected'   => ['label' => 'Ditolak',   'cls' => 'red',   'ico' => 'fa-circle-xmark'],
    'processing' => ['label' => 'Diproses',  'cls' => 'amber', 'ico' => 'fa-spinner'],
];
@endphp

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Riwayat Request Dokumen</h5>
            <div class="sima-card__subtitle">Semua permintaan dokumen yang pernah diajukan</div>
        </div>
        <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Request Baru
        </a>
    </div>

    @if($requests->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-inbox" style="font-size:36px;margin-bottom:12px;display:block;opacity:.35"></i>
            <div style="font-size:14px;font-weight:500">Belum ada request yang diajukan</div>
            <div style="font-size:12.5px;margin-top:4px">Ajukan permintaan dokumen pertama Anda ke KLN</div>
            <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--sm" style="margin-top:16px">
                <i class="fas fa-paper-plane"></i> Ajukan Request
            </a>
        </div>
    @else
        <div class="sima-card__body" style="padding:0">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:90px">Kode</th>
                        <th>Jenis Dokumen</th>
                        <th>Keperluan</th>
                        <th style="width:110px">Tanggal</th>
                        <th style="width:110px">Status</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    @php
                        $st   = $req->status ?? 'pending';
                        $info = $statusMap[$st] ?? $statusMap['pending'];
                        $code = 'REQ-' . str_pad($req->id, 3, '0', STR_PAD_LEFT);
                        $nama = str_replace('_', ' ', $req->namaDkmn ?? $req->tipeDkmn ?? '-');
                        $tgl  = \Carbon\Carbon::parse($req->created_at)->format('d M Y');
                    @endphp
                    <tr>
                        <td><span style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $code }}</span></td>
                        <td style="font-weight:600;font-size:13.5px">{{ $nama }}</td>
                        <td style="font-size:12.5px;color:var(--c-text-2);max-width:220px">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $req->message }}">
                                {{ $req->message }}
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $tgl }}</td>
                        <td>
                            <span class="sima-badge sima-badge--{{ $info['cls'] }}">
                                <i class="fas {{ $info['ico'] }}"></i> {{ $info['label'] }}
                            </span>
                        </td>
                        <td>
                            <button class="sima-btn sima-btn--sm sima-btn--outline btn-detail"
                                    data-id="{{ $req->id }}"
                                    data-code="{{ $code }}"
                                    data-nama="{{ $nama }}"
                                    data-tgl="{{ $tgl }}"
                                    data-status="{{ $st }}"
                                    data-status-label="{{ $info['label'] }}"
                                    data-status-cls="{{ $info['cls'] }}"
                                    data-keterangan="{{ $req->keterangan ?? '' }}"
                                    data-has-file="{{ $req->has_file ? '1' : '0' }}"
                                    data-message="{{ $req->message ?? '' }}"
                                    style="font-size:11.5px;padding:4px 10px">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── MODAL DETAIL ─────────────────────────────────────────── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:500px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">

        {{-- Header --}}
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div id="dm-code" style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3);margin-bottom:3px"></div>
                <div id="dm-nama" style="font-size:16px;font-weight:700;color:var(--c-text-1)"></div>
            </div>
            <button onclick="closeDetail()" style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b;font-size:14px">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:20px 24px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div style="padding:12px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Tanggal</div>
                    <div id="dm-tgl" style="font-size:13.5px;font-weight:600;color:#1e293b"></div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:6px">Status</div>
                    <div id="dm-status"></div>
                </div>
            </div>

            <div style="margin-bottom:14px">
                <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:6px">Keperluan</div>
                <div id="dm-message" style="font-size:13px;color:#334155;background:#f8fafc;border-radius:10px;padding:12px;border:1px solid #f1f5f9;line-height:1.55"></div>
            </div>

            {{-- Rejection reason --}}
            <div id="dm-reject-box" style="display:none;margin-bottom:14px">
                <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#ef4444;margin-bottom:6px">
                    <i class="fas fa-circle-xmark"></i> Alasan Penolakan
                </div>
                <div id="dm-keterangan" style="font-size:13px;color:#7f1d1d;background:#fef2f2;border-radius:10px;padding:12px;border:1px solid rgba(220,38,38,.15);line-height:1.55"></div>
            </div>

            {{-- File from KLN --}}
            <div id="dm-file-box" style="display:none;margin-bottom:14px">
                <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#059669;margin-bottom:6px">
                    <i class="fas fa-file-circle-check"></i> Dokumen dari KLN
                </div>
                <a id="dm-file-link" href="#" target="_blank"
                   style="display:flex;align-items:center;gap:10px;padding:12px;background:#ecfdf5;border:1px solid rgba(5,150,105,.2);border-radius:10px;color:#065f46;text-decoration:none;font-size:13px;font-weight:500">
                    <i class="fas fa-file-arrow-down" style="font-size:18px;color:#059669"></i>
                    <span>Unduh dokumen yang dikirim KLN</span>
                    <i class="fas fa-arrow-down" style="margin-left:auto;font-size:11px;opacity:.6"></i>
                </a>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding:14px 24px 20px;display:flex;gap:8px">
            <a href="{{ route('mahasiswa.request.create') }}" class="sima-btn sima-btn--sm" id="dm-btn-ulang" style="display:none">
                <i class="fas fa-rotate-right"></i> Ajukan Ulang
            </a>
            <button onclick="closeDetail()" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-xmark"></i> Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = this.dataset;
        document.getElementById('dm-code').textContent    = d.code;
        document.getElementById('dm-nama').textContent    = d.nama;
        document.getElementById('dm-tgl').textContent     = d.tgl;
        document.getElementById('dm-message').textContent = d.message;

        const clsMap = { blue:'#1e40af', green:'#065f46', red:'#991b1b', amber:'#92400e' };
        const bgMap  = { blue:'#eff6ff', green:'#ecfdf5', red:'#fef2f2', amber:'#fffbeb' };
        document.getElementById('dm-status').innerHTML =
            `<span style="font-size:12.5px;font-weight:600;padding:4px 12px;border-radius:100px;background:${bgMap[d.statusCls]||'#f1f5f9'};color:${clsMap[d.statusCls]||'#334155'}">${d.statusLabel}</span>`;

        const rejectBox = document.getElementById('dm-reject-box');
        if (d.status === 'rejected' && d.keterangan) {
            document.getElementById('dm-keterangan').textContent = d.keterangan;
            rejectBox.style.display = 'block';
        } else {
            rejectBox.style.display = 'none';
        }

        const fileBox = document.getElementById('dm-file-box');
        if (d.hasFile === '1') {
            document.getElementById('dm-file-link').href = `/mahasiswa/request/${d.id}/file`;
            fileBox.style.display = 'block';
        } else {
            fileBox.style.display = 'none';
        }

        document.getElementById('dm-btn-ulang').style.display = d.status === 'rejected' ? 'inline-flex' : 'none';
        document.getElementById('detailModal').style.display = 'flex';
    });
});

function closeDetail() {
    document.getElementById('detailModal').style.display = 'none';
}

document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetail();
});
</script>
@endsection
