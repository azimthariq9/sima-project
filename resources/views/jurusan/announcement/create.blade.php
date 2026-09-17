@extends('layouts.sima')

@section('page_title',    'Create Announcement')
@section('page_section',  'JURUSAN')
@section('page_subtitle', 'Write a new announcement')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('jurusan.announcement.index') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
        <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Create Announcement</h5>
        <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
            Active announcements will be visible to students immediately.
        </div>
    </div>

    <form method="POST" action="{{ route('jurusan.announcement.store') }}" style="padding:24px;">
        @csrf

        @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;
                    padding:12px 16px;margin-bottom:20px;color:var(--c-red);font-size:13px;">
            <strong>There are errors:</strong>
            <ul style="margin:6px 0 0 16px;padding:0;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:22px;">

            {{-- Title --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Title <span style="color:var(--c-red)">*</span>
                </label>
                <input type="text" name="subject" class="sima-input mt-1"
                       value="{{ old('subject') }}"
                       placeholder="Announcement title…"
                       required maxlength="200">
            </div>

            {{-- Message --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Message <span style="color:var(--c-red)">*</span>
                </label>
                <textarea name="message" class="sima-input mt-1" rows="5"
                          placeholder="Write your announcement here…"
                          required style="resize:vertical;">{{ old('message') }}</textarea>
            </div>

            {{-- Priority --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Priority
                </label>
                <div style="margin-top:10px;display:flex;align-items:center;gap:10px;">
                    <input type="checkbox" name="is_penting" value="1" id="isPenting"
                           {{ old('is_penting') ? 'checked' : '' }}
                           style="width:18px;height:18px;accent-color:var(--c-red);cursor:pointer;">
                    <label for="isPenting"
                           style="font-size:13px;color:var(--c-text-2);cursor:pointer;line-height:1.4;">
                        <i class="fas fa-thumbtack" style="color:var(--c-accent);margin-right:4px;"></i>
                        Mark as Priority (pinned at top with indicator)
                    </label>
                </div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                <a href="{{ route('jurusan.announcement.index') }}" class="sima-btn sima-btn--outline">
                    Cancel
                </a>
                <button type="submit" class="sima-btn sima-btn--accent">
                    <i class="fas fa-paper-plane me-1"></i> Publish
                </button>
            </div>

        </div>
    </form>
</div>

@endsection
