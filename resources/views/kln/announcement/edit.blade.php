@extends('layouts.sima')

@section('page_title',    'Edit Pengumuman')
@section('page_section',  'PENGUMUMAN')
@section('page_subtitle', 'Perbarui isi atau status pengumuman')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('kln.announcement') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="sima-card">
    <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Edit Pengumuman</h5>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
                ID #{{ $ann->id }} &middot; Dibuat {{ \Carbon\Carbon::parse($ann->created_at)->isoFormat('D MMM YYYY') }}
            </div>
        </div>
        @php
            $statusMap = [
                'active'   => ['label' => 'Aktif',    'cls' => 'sima-badge--green'],
                'inactive' => ['label' => 'Nonaktif', 'cls' => 'sima-badge--red'],
                'draft'    => ['label' => 'Draft',    'cls' => 'sima-badge--amber'],
            ];
            $s = $statusMap[$ann->status] ?? ['label' => $ann->status, 'cls' => 'sima-badge--amber'];
        @endphp
        <span class="sima-badge {{ $s['cls'] }}">{{ $s['label'] }}</span>
    </div>

    <form method="POST" action="{{ route('kln.announcement.update', $ann->id) }}"
          enctype="multipart/form-data" style="padding:24px;">
        @csrf

        @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;
                    padding:12px 16px;margin-bottom:20px;color:var(--c-red);font-size:13px;">
            <strong>Terdapat kesalahan:</strong>
            <ul style="margin:6px 0 0 16px;padding:0;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
        @endif

        <div style="display:flex;flex-direction:column;gap:22px;">

            {{-- Judul --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Judul Pengumuman
                </label>
                <input type="text" name="subject" class="sima-input mt-1"
                       value="{{ old('subject', $ann->subject) }}"
                       placeholder="Judul pengumuman" required>
            </div>

            {{-- Isi --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Isi Pengumuman
                </label>
                <textarea name="message" class="sima-input mt-1" rows="10"
                          required style="resize:vertical;min-height:220px;line-height:1.7;">{{ old('message', $ann->message) }}</textarea>
            </div>

            {{-- Status + Penting --}}
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Status Publikasi
                    </label>
                    <select name="status" class="sima-input mt-1" required>
                        @php $curStatus = old('status', $ann->status); @endphp
                        <option value="draft"    {{ $curStatus === 'draft'    ? 'selected' : '' }}>
                            Draft — belum tampil ke mahasiswa
                        </option>
                        <option value="active"   {{ $curStatus === 'active'   ? 'selected' : '' }}>
                            Aktif — langsung tampil
                        </option>
                        <option value="inactive" {{ $curStatus === 'inactive' ? 'selected' : '' }}>
                            Nonaktif — sembunyikan
                        </option>
                    </select>
                </div>
                <div class="col-12 col-md-6">
                    <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                        Tandai Penting
                    </label>
                    <div style="margin-top:12px;display:flex;align-items:center;gap:10px;">
                        <input type="checkbox" name="is_penting" id="isPenting" value="1"
                               {{ old('is_penting', $ann->is_penting) ? 'checked' : '' }}
                               style="width:18px;height:18px;accent-color:var(--c-red);cursor:pointer;">
                        <label for="isPenting"
                               style="font-size:13px;color:var(--c-text-2);cursor:pointer;line-height:1.4;">
                            Tandai sebagai pengumuman penting<br>
                            <span style="font-size:11px;color:var(--c-text-3);">Akan ditampilkan dengan badge merah</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Existing files --}}
            @if($files->isNotEmpty())
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Lampiran Saat Ini
                </label>
                <div style="margin-top:8px;display:flex;flex-direction:column;gap:6px;" id="existingFiles">
                    @foreach($files as $file)
                    @php $isImg = str_starts_with($file->mimeType ?? '', 'image/'); @endphp
                    <div id="efile-{{ $file->id }}"
                         style="display:flex;align-items:center;gap:10px;padding:8px 14px;
                                background:var(--c-bg-2);border-radius:8px;font-size:13px;">
                        <i class="fas {{ $isImg ? 'fa-image' : 'fa-file-alt' }}"
                           style="color:var(--c-accent);width:16px;"></i>
                        <a href="{{ route('kln.announcement.file', [$ann->id, $file->id]) }}"
                           target="_blank"
                           style="flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;color:var(--c-text-1);">
                            {{ $file->originalName }}
                        </a>
                        <span style="color:var(--c-text-3);font-size:11px;white-space:nowrap;">
                            {{ number_format(($file->size ?? 0) / 1024, 0) }} KB
                        </span>
                        <button type="button" onclick="deleteFile({{ $file->id }})"
                                style="border:none;background:none;color:var(--c-red);cursor:pointer;
                                       padding:2px 8px;border-radius:4px;font-size:13px;"
                                title="Hapus lampiran">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Add new files --}}
            <div>
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                    Tambah Lampiran Baru <span style="font-weight:400;color:var(--c-text-3);">(opsional)</span>
                </label>
                <div style="margin-top:8px;border:2px dashed var(--c-border);border-radius:10px;
                            padding:24px 20px;text-align:center;cursor:pointer;transition:border-color .2s;"
                     onclick="document.getElementById('fileInput').click()"
                     id="dropZone">
                    <input type="file" name="files[]" id="fileInput" multiple
                           accept="image/*,.pdf,.doc,.docx"
                           style="display:none;" onchange="previewFiles(this)">
                    <i class="fas fa-cloud-upload-alt fa-lg" style="color:var(--c-accent);display:block;margin-bottom:8px;"></i>
                    <div style="font-size:13px;font-weight:600;color:var(--c-text-2);">Klik untuk pilih file</div>
                    <div style="font-size:11px;color:var(--c-text-3);margin-top:3px;">
                        Gambar, PDF, DOC &middot; Maks. 5 MB per file
                    </div>
                </div>
                <div id="filePreview" style="margin-top:10px;display:flex;flex-direction:column;gap:6px;"></div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                <a href="{{ route('kln.announcement') }}" class="sima-btn sima-btn--outline">Batal</a>
                <button type="submit" class="sima-btn sima-btn--accent">
                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
function previewFiles(input) {
    const preview = document.getElementById('filePreview');
    preview.innerHTML = '';
    [...input.files].forEach(file => {
        const isImg = file.type.startsWith('image/');
        const el    = document.createElement('div');
        el.style.cssText = 'display:flex;align-items:center;gap:10px;padding:8px 14px;' +
                           'background:var(--c-bg-2);border-radius:8px;font-size:13px;';
        el.innerHTML = `<i class="fas ${isImg ? 'fa-image' : 'fa-file-alt'}" style="color:var(--c-accent);width:16px;"></i>
                        <span style="flex:1;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">${file.name}</span>
                        <span style="color:var(--c-text-3);font-size:11px;white-space:nowrap;">
                            ${(file.size / 1024).toFixed(0)} KB
                        </span>`;
        preview.appendChild(el);
    });
}

const zone = document.getElementById('dropZone');
zone.addEventListener('mouseover', () => zone.style.borderColor = 'var(--c-accent)');
zone.addEventListener('mouseout',  () => zone.style.borderColor = 'var(--c-border)');

function deleteFile(fileId) {
    if (!confirm('Hapus lampiran ini?')) return;
    fetch(`/kln/announcement/file/${fileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('efile-' + fileId)?.remove();
        }
    })
    .catch(() => alert('Gagal menghapus lampiran.'));
}
</script>
@endpush
