@extends('layouts.sima')

@section('page_title',    'Schedules')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Manage class schedules in your department')

@section('main_content')

@php
$tahunSekarang  = (int) date('Y');
$tahunAjarList  = [];
for ($i = -1; $i <= 10; $i++) {
    $a = $tahunSekarang + $i;
    $tahunAjarList[] = "{$a}/".($a+1);
}
$tahunAjarDefault = date('Y').'/'.(date('Y')+1);
$hariOrder = ['Senin'=>1,'Selasa'=>2,'Rabu'=>3,'Kamis'=>4,'Jumat'=>5,'Sabtu'=>6];
@endphp

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Schedule List</h5>
            <div class="sima-card__subtitle">Total {{ $jadwal->count() }} schedules</div>
        </div>
        <a href="{{ route('jurusan.jadwal.create') }}" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Add Schedule
        </a>
    </div>

    {{-- Filter --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <select name="hari" class="sima-input" style="width:140px;font-size:13px" onchange="this.form.submit()">
                <option value="">— All Days —</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                    <option value="{{ $hari }}" {{ request('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div style="overflow-x:auto">
        <table class="sima-table" data-datatable>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:90px">Day</th>
                        <th style="width:110px">Time</th>
                        <th>Course</th>
                        <th>Lecturer</th>
                        <th style="width:90px">Class</th>
                        <th style="width:80px">Room</th>
                        <th style="width:60px;text-align:center">Sessions</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $hariColor = [
                        'Senin'=>'#2563EB','Selasa'=>'#0D9488','Rabu'=>'#7C3AED',
                        'Kamis'=>'#D97706','Jumat'=>'#DC2626','Sabtu'=>'#059669',
                    ];
                    @endphp
                    @foreach($jadwal as $j)
                    @php $c = $hariColor[$j->hari] ?? '#888'; @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            <span style="background:{{ $c }}18;color:{{ $c }};border:1px solid {{ $c }}40;
                                         padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600">
                                {{ $j->hari ?? '—' }}
                            </span>
                        </td>
                        <td style="font-family:var(--f-mono);font-size:12px;font-weight:600">{{ $j->jam ?? '—' }}</td>
                        <td style="font-size:13px;font-weight:600">{{ $j->nama_matkul ?? '—' }}</td>
                        <td style="font-size:12.5px;color:var(--c-text-2)">{{ $j->nama_dosen ?? '—' }}</td>
                        <td>
                            <span style="font-family:var(--f-mono);background:var(--c-teal-lt);color:var(--c-teal);padding:3px 8px;border-radius:6px;font-size:12px">
                                {{ $j->kodeKelas ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $j->ruangan ?? '—' }}</td>
                        <td style="text-align:center;font-family:var(--f-mono);font-size:13px">{{ $j->totalSesi ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-jadwal"
                                        data-id="{{ $j->id }}" style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-jadwal"
                                        data-id="{{ $j->id }}"
                                        style="font-size:11.5px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
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
const CSRF = '{{ csrf_token() }}';

(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('created') === '1' || params.get('updated') === '1') {
        const msg = params.get('created') === '1' ? 'Schedule created successfully.' : 'Schedule updated successfully.';
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#059669;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.15);display:flex;align-items:center;gap:8px;animation:simaFadeIn .3s';
        toast.innerHTML = '<i class="fas fa-circle-check"></i> ' + msg;
        document.body.appendChild(toast);
        window.history.replaceState({}, '', window.location.pathname);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
})();

document.querySelectorAll('.btn-edit-jadwal').forEach(btn => {
    btn.addEventListener('click', function() {
        window.location.href = `/jurusan/jadwal/${this.dataset.id}/edit`;
    });
});

document.querySelectorAll('.btn-del-jadwal').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        if (!confirm('Delete this schedule?')) return;
        fetch(`/jurusan/jadwal/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); })
        .catch(err => alert(err.message));
    });
});
</script>
@endsection
