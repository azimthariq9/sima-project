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
        <button onclick="document.getElementById('modalCreate').style.display='flex'"
                class="sima-btn sima-btn--sm">
            <i class="fas fa-plus"></i> Create Announcement
        </button>
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


{{-- ══════════════════════════════════════
     CREATE ANNOUNCEMENT MODAL
══════════════════════════════════════ --}}
<div id="modalCreate" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:520px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Create Announcement</div>
            <button onclick="document.getElementById('modalCreate').style.display='none'"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form method="POST" action="{{ route('jurusan.announcement.store') }}">
                @csrf
                <div style="margin-bottom:14px">
                    <label class="sima-label">Title <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="subject" class="sima-input" placeholder="Announcement title…" required maxlength="200">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Message <span style="color:var(--c-red)">*</span></label>
                    <textarea name="message" class="sima-input" rows="5"
                              placeholder="Write your announcement here…"
                              required style="resize:vertical"></textarea>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:18px">
                    <input type="checkbox" name="is_penting" value="1" id="create-penting"
                           style="width:16px;height:16px;cursor:pointer">
                    <label for="create-penting" class="sima-label" style="margin-bottom:0;cursor:pointer">
                        <i class="fas fa-thumbtack" style="color:var(--c-accent);margin-right:4px"></i>
                        Mark as Priority (pinned at top with indicator)
                    </label>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-paper-plane"></i> Publish</button>
                    <button type="button" onclick="document.getElementById('modalCreate').style.display='none'"
                            class="sima-btn sima-btn--outline">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     EDIT ANNOUNCEMENT MODAL
══════════════════════════════════════ --}}
<div id="modalEdit" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:520px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div style="font-size:15px;font-weight:700;color:#1e293b">Edit Announcement</div>
            <button onclick="document.getElementById('modalEdit').style.display='none'"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div style="padding:20px 24px">
            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')
                <div style="margin-bottom:14px">
                    <label class="sima-label">Title <span style="color:var(--c-red)">*</span></label>
                    <input type="text" name="subject" id="edit-subject" class="sima-input" required maxlength="200">
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Message <span style="color:var(--c-red)">*</span></label>
                    <textarea name="message" id="edit-message" class="sima-input" rows="5"
                              required style="resize:vertical"></textarea>
                </div>
                <div style="margin-bottom:14px">
                    <label class="sima-label">Status</label>
                    <select name="status" id="edit-status" class="sima-input">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:18px">
                    <input type="checkbox" name="is_penting" value="1" id="edit-penting"
                           style="width:16px;height:16px;cursor:pointer">
                    <label for="edit-penting" class="sima-label" style="margin-bottom:0;cursor:pointer">
                        <i class="fas fa-thumbtack" style="color:var(--c-accent);margin-right:4px"></i>
                        Mark as Priority
                    </label>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit" class="sima-btn"><i class="fas fa-save"></i> Save</button>
                    <button type="button" onclick="document.getElementById('modalEdit').style.display='none'"
                            class="sima-btn sima-btn--outline">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.getElementById('modalCreate').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
document.getElementById('modalEdit').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;

        fetch(`{{ url('jurusan/announcement') }}/${id}/edit`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('edit-subject').value  = data.subject  || '';
                document.getElementById('edit-message').value  = data.message  || '';
                document.getElementById('edit-status').value   = data.status   || 'active';
                document.getElementById('edit-penting').checked = !!data.is_penting;
                document.getElementById('editForm').action = `{{ url('jurusan/announcement') }}/${id}`;
                document.getElementById('modalEdit').style.display = 'flex';
            });
    });
});
</script>
@endsection
