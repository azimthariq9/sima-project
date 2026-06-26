@extends('layouts.sima')

@section('page_title',    'Request Dokumen')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Daftar permintaan dokumen dari mahasiswa')

@section('main_content')

@if(session('success'))
<div style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.25);border-radius:10px;
            padding:12px 16px;margin-bottom:16px;font-size:13px;color:#065f46;display:flex;align-items:center;gap:8px">
    <i class="fas fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Request Dokumen</h5>
            <div class="sima-card__subtitle">Permintaan dokumen dari mahasiswa di jurusan ini</div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama/NPM/jenis dokumen…"
                   class="sima-input" style="width:260px;font-size:13px">
            <select name="filter" class="sima-input" style="width:150px;font-size:13px" onchange="this.form.submit()">
                <option value="">— Semua Status —</option>
                <option value="pending"  {{ $filter === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $filter === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search || $filter)
                <a href="{{ route('jurusan.request.index') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($requests->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-inbox" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ $search || $filter ? 'Tidak ada request yang cocok' : 'Belum ada request dokumen' }}
            </div>
        </div>
    @else
        <div class="sima-card__body" style="padding:0">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Mahasiswa</th>
                        <th>Jenis Dokumen</th>
                        <th>Keperluan</th>
                        <th style="width:110px">Tanggal</th>
                        <th style="width:100px">Status</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    @php
                        $stMap = [
                            'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Pending'],
                            'approved' => ['cls'=>'sima-badge--green', 'label'=>'Disetujui'],
                            'rejected' => ['cls'=>'sima-badge--red',   'label'=>'Ditolak'],
                        ];
                        $st = $stMap[$req->status] ?? $stMap['pending'];
                    @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $requests->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13.5px">{{ $req->nama_mahasiswa }}</div>
                            <div style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $req->npm }}</div>
                        </td>
                        <td style="font-weight:600;font-size:13px">
                            {{ str_replace('_', ' ', $req->tipeDkmn ?? '-') }}
                        </td>
                        <td style="font-size:12.5px;color:var(--c-text-2);max-width:200px">
                            <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                                 title="{{ $req->message }}">
                                {{ $req->message ?? '—' }}
                            </div>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">
                            {{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <span class="sima-badge {{ $st['cls'] }}" style="font-size:11px">{{ $st['label'] }}</span>
                        </td>
                        <td>
                            <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-detail"
                                    data-req="{{ json_encode([
                                        'id'          => $req->id,
                                        'mahasiswa'   => $req->nama_mahasiswa,
                                        'npm'         => $req->npm,
                                        'tipeDkmn'    => str_replace('_', ' ', $req->tipeDkmn ?? '-'),
                                        'message'     => $req->message ?? '',
                                        'status'      => $req->status,
                                        'keterangan'  => $req->keterangan ?? '',
                                        'created_at'  => \Carbon\Carbon::parse($req->created_at)->format('d M Y'),
                                    ]) }}"
                                    style="font-size:11.5px;padding:4px 10px">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:14px 20px">
            {{ $requests->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>


{{-- ══════════════════════════════════════
     MODAL DETAIL + PROSES
══════════════════════════════════════ --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:520px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden;max-height:90vh;overflow-y:auto">

        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Detail Request</div>
            <button onclick="document.getElementById('detailModal').style.display='none'"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div style="padding:12px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Mahasiswa</div>
                    <div id="dm-mahasiswa" style="font-size:13.5px;font-weight:600;color:#1e293b"></div>
                    <div id="dm-npm" style="font-size:11.5px;color:#64748b;font-family:monospace"></div>
                </div>
                <div style="padding:12px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Jenis Dokumen</div>
                    <div id="dm-tipeDkmn" style="font-size:13.5px;font-weight:600;color:#1e293b"></div>
                    <div id="dm-date" style="font-size:11.5px;color:#64748b"></div>
                </div>
            </div>

            <div style="margin-bottom:16px">
                <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:6px">Keperluan</div>
                <div id="dm-message" style="font-size:13px;color:#334155;background:#f8fafc;border-radius:10px;padding:12px;border:1px solid #f1f5f9;line-height:1.55"></div>
            </div>

            {{-- Upload file --}}
            <div id="dm-upload-box" style="margin-bottom:16px">
                <div style="font-size:12px;font-weight:600;color:var(--c-text-2);margin-bottom:8px">Upload Dokumen ke Mahasiswa</div>
                <form id="uploadForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="display:flex;gap:8px;align-items:center">
                        <input type="file" name="file" class="sima-input" accept=".pdf,.jpg,.jpeg,.png"
                               style="padding:7px 12px;font-size:12.5px;flex:1" required>
                        <button type="submit" class="sima-btn sima-btn--sm">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </form>
            </div>

            {{-- Status update --}}
            <div id="dm-status-box">
                <div style="font-size:12px;font-weight:600;color:var(--c-text-2);margin-bottom:8px">Perbarui Status</div>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div style="margin-bottom:10px">
                        <select name="status" class="sima-input" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                        </select>
                    </div>
                    <div style="margin-bottom:10px">
                        <textarea name="message" class="sima-input" rows="2"
                                  placeholder="Keterangan (opsional, khusus penolakan)" style="resize:vertical"></textarea>
                    </div>
                    <button type="submit" class="sima-btn sima-btn--sm">
                        <i class="fas fa-rotate"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.req);
        document.getElementById('dm-mahasiswa').textContent = d.mahasiswa;
        document.getElementById('dm-npm').textContent       = d.npm;
        document.getElementById('dm-tipeDkmn').textContent  = d.tipeDkmn;
        document.getElementById('dm-date').textContent      = d.created_at;
        document.getElementById('dm-message').textContent   = d.message || '—';

        const baseUrl = `{{ url('jurusan/requestDok') }}/${d.id}`;
        document.getElementById('uploadForm').action = `${baseUrl}/upload`;
        document.getElementById('statusForm').action = `${baseUrl}/status`;

        // Sembunyikan upload & status box jika sudah final
        const isFinal = d.status === 'approved' || d.status === 'rejected';
        document.getElementById('dm-upload-box').style.display = isFinal ? 'none' : 'block';

        document.getElementById('detailModal').style.display = 'flex';
    });
});
</script>
@endsection
