@extends('layouts.sima')

@section('page_title',    'Courses')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Manage courses in your department')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Course List</h5>
            <div class="sima-card__subtitle">Total {{ $matakuliah->count() }} courses</div>
        </div>
        <a href="{{ route('jurusan.matakuliah.create') }}" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Add Course
        </a>
    </div>

    <div style="overflow-x:auto">
            <table class="sima-table" data-datatable>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th style="width:110px">Course Code</th>
                        <th>Course Name</th>
                        <th style="width:60px;text-align:center">Credits</th>
                        <th style="width:80px">Desc.</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($matakuliah as $mk)
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            <span style="font-family:var(--f-mono);font-size:12px;background:var(--c-blue-lt);color:var(--c-blue);padding:3px 8px;border-radius:6px">
                                {{ $mk->kodeMk ?? '—' }}
                            </span>
                        </td>
                        <td style="font-weight:600;font-size:13.5px">{{ $mk->namaMk ?? '—' }}</td>
                        <td style="text-align:center;font-family:var(--f-mono);font-size:13px">{{ $mk->sks ?? '—' }}</td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $mk->keterangan ?? '—' }}</td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-edit-mk"
                                        data-id="{{ $mk->id }}"
                                        style="font-size:11.5px;padding:4px 10px">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <button type="button" class="sima-btn sima-btn--sm btn-del-mk"
                                        data-id="{{ $mk->id }}" data-nama="{{ $mk->namaMk }}"
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
        const msg = params.get('created') === '1' ? 'Course created successfully.' : 'Course updated successfully.';
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#059669;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.15);display:flex;align-items:center;gap:8px;animation:simaFadeIn .3s';
        toast.innerHTML = '<i class="fas fa-circle-check"></i> ' + msg;
        document.body.appendChild(toast);
        window.history.replaceState({}, '', window.location.pathname);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
})();

document.querySelectorAll('.btn-edit-mk').forEach(btn => {
    btn.addEventListener('click', function() {
        window.location.href = `/jurusan/matakuliah/${this.dataset.id}/edit`;
    });
});

document.querySelectorAll('.btn-del-mk').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id; const nama = this.dataset.nama;
        if (!confirm(`Delete course "${nama}"?`)) return;
        fetch(`/jurusan/matakuliah/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(res => { if (res.success) window.location.reload(); else alert(res.message); });
    });
});
</script>
@endsection
