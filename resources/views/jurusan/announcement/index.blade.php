@extends('layouts.sima')

@section('page_title',    'Announcements')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Manage announcements from your department')

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
            <h5 class="sima-card__title">Department Announcements</h5>
            <div class="sima-card__subtitle">Announcements published by your department</div>
        </div>
        <a href="{{ route('jurusan.announcement.create') }}" class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Create Announcement
        </a>
    </div>

    {{-- Filter bar --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <select name="filter" class="sima-input" style="width:150px;font-size:13px" onchange="this.form.submit()">
                <option value="">— All Status —</option>
                <option value="active"   {{ $filter === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $filter === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="penting"  {{ $filter === 'penting'  ? 'selected' : '' }}>Priority</option>
            </select>
        </form>
    </div>

    <div class="sima-card__body" style="padding:0">
            <table class="sima-table" data-datatable>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Title</th>
                        <th style="width:100px">Status</th>
                        <th style="width:80px;text-align:center">Priority</th>
                        <th style="width:110px">Date</th>
                        <th style="width:100px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $ann)
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $loop->index + 1 }}
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13.5px;color:var(--c-text-1)">{{ $ann->subject }}</div>
                            <div style="font-size:12px;color:var(--c-text-3);margin-top:2px">
                                {{ Str::limit($ann->message, 80) }}
                            </div>
                        </td>
                        <td>
                            <span class="sima-badge {{ $ann->status === 'active' ? 'sima-badge--green' : 'sima-badge--grey' }}">
                                {{ $ann->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="text-align:center">
                            @if($ann->is_penting)
                                <i class="fas fa-thumbtack" style="color:var(--c-accent);font-size:14px" title="Priority"></i>
                            @else
                                <span style="color:var(--c-text-3);font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">
                            {{ \Carbon\Carbon::parse($ann->created_at)->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button type="button"
                                        class="sima-btn sima-btn--sm sima-btn--outline btn-edit"
                                        style="font-size:11.5px;padding:4px 10px"
                                        data-id="{{ $ann->id }}"
                                        title="Edit">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('jurusan.announcement.destroy', $ann->id) }}"
                                      onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="sima-btn sima-btn--sm"
                                            style="font-size:11.5px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)"
                                            title="Delete">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
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
(function() {
    const params = new URLSearchParams(window.location.search);
    const flashCreated = @json(session('flash_created'));
    const flashSuccess = @json(session('success'));
    if (params.get('created') === '1' || flashCreated || flashSuccess) {
        const msg = flashCreated || flashSuccess || 'Announcement created successfully.';
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#059669;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:600;box-shadow:0 4px 12px rgba(0,0,0,.15);display:flex;align-items:center;gap:8px;animation:simaFadeIn .3s';
        toast.innerHTML = '<i class="fas fa-circle-check"></i> ' + msg;
        document.body.appendChild(toast);
        window.history.replaceState({}, '', window.location.pathname);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
})();

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        window.location.href = `/jurusan/announcement/${this.dataset.id}/edit`;
    });
});
</script>
@endsection
