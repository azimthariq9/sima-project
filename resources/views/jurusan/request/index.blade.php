@extends('layouts.sima')

@section('page_title',    'Document Requests')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Document requests from students')

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
            <h5 class="sima-card__title">Document Requests</h5>
            <div class="sima-card__subtitle">Document requests from students in your department</div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <select name="filter" class="sima-input" style="width:150px;font-size:13px" onchange="this.form.submit()">
                <option value="">— All Status —</option>
                <option value="pending"  {{ $filter === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $filter === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </form>
    </div>

    <div class="sima-card__body" style="padding:0">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Student</th>
                    <th>Document Type</th>
                    <th>Purpose</th>
                    <th style="width:110px">Date</th>
                    <th style="width:100px">Status</th>
                    <th style="width:80px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                @php
                    $stMap = [
                        'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Pending'],
                        'approved' => ['cls'=>'sima-badge--green', 'label'=>'Approved'],
                        'rejected' => ['cls'=>'sima-badge--red',   'label'=>'Rejected'],
                    ];
                    $st = $stMap[$req->status] ?? $stMap['pending'];
                @endphp
                <tr>
                    <td style="color:var(--c-text-3);font-size:12px">
                        {{ $loop->index + 1 }}
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
</div>




@endsection

@section('page_js')
<script>
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.req);
        window.location.href = `/jurusan/requestDok/${d.id}`;
    });
});
</script>
@endsection
