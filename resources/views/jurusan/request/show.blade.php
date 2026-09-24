@extends('layouts.sima')

@section('page_title',   'Request Detail')
@section('page_section', 'JURUSAN')
@section('page_subtitle','Review and process document request')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('jurusan.request.index') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row g-3">

    <div class="col-md-7 sima-fade">
        <div class="sima-card">
            <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
                <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Request Detail</h5>
                <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
                    Request #{{ $req->id }} — submitted {{ $req->created_at?->diffForHumans() ?? $req->created_at }}
                </div>
            </div>

            <div style="padding:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
                    <div style="padding:14px;background:var(--c-bg-alt);border-radius:10px;border:1px solid var(--c-border-soft);">
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:6px;">Student</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);">{{ $req->mahasiswa?->nama ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--c-text-3);font-family:var(--f-mono);margin-top:2px;">{{ $req->npm ?? '—' }}</div>
                    </div>
                    <div style="padding:14px;background:var(--c-bg-alt);border-radius:10px;border:1px solid var(--c-border-soft);">
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:6px;">Document Type</div>
                        <div style="font-size:14px;font-weight:600;color:var(--c-text-1);">{{ str_replace('_', ' ', $req->tipeDkmn) }}</div>
                        <div style="font-size:12px;color:var(--c-text-3);margin-top:2px;">{{ $req->created_at?->format('d M Y') ?? '—' }}</div>
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:6px;">Purpose</div>
                    <div style="font-size:13px;color:var(--c-text-1);background:var(--c-bg-alt);border-radius:10px;padding:14px;border:1px solid var(--c-border-soft);line-height:1.6;">
                        {{ $req->message ?? '—' }}
                    </div>
                </div>

                @if(!in_array($req->status, ['approved', 'rejected']))

                {{-- Upload file --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:12px;font-weight:600;color:var(--c-text-2);margin-bottom:8px;">Upload Document to Student</div>
                    <form method="POST" action="{{ route('jurusan.request.upload', $req->id) }}" enctype="multipart/form-data">
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
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--c-text-2);margin-bottom:8px;">Update Status</div>
                    <form method="POST" action="{{ route('jurusan.request.status', $req->id) }}">
                        @csrf
                        @method('PATCH')
                        <div style="margin-bottom:10px;">
                            <select name="status" class="sima-input" required>
                                <option value="pending" {{ $req->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $req->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $req->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div style="margin-bottom:10px;">
                            <textarea name="message" class="sima-input" rows="2"
                                      placeholder="Notes (optional, for rejection)" style="resize:vertical"></textarea>
                        </div>
                        <button type="submit" class="sima-btn sima-btn--sm">
                            <i class="fas fa-rotate"></i> Update Status
                        </button>
                    </form>
                </div>

                @else

                <div style="padding:14px;background:var(--c-bg-alt);border-radius:10px;border:1px solid var(--c-border-soft);text-align:center;">
                    <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:8px;">Final Status</div>
                    <span class="sima-badge sima-badge--{{ $req->status === 'approved' ? 'green' : 'red' }}" style="font-size:13px;">
                        {{ ucfirst($req->status) }}
                    </span>
                </div>

                @endif
            </div>
        </div>
    </div>

    <div class="col-md-5 sima-fade sima-fade--1">
        <div class="sima-card">
            <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
                <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Request Info</h5>
                <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">Details and metadata</div>
            </div>
            <div style="padding:20px 24px;">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:3px;">Request ID</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);font-family:var(--f-mono);">#{{ $req->id }}</div>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:3px;">Submitted</div>
                        <div style="font-size:13px;font-weight:600;color:var(--c-text-1);">{{ $req->created_at?->format('d M Y, H:i') ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10.5px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:var(--c-text-3);margin-bottom:3px;">Current Status</div>
                        <span class="sima-badge sima-badge--{{ $req->status === 'approved' ? 'green' : ($req->status === 'rejected' ? 'red' : 'blue') }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
