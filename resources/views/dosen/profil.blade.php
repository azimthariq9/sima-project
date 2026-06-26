@extends('layouts.sima')

@section('page_title',    'Profil Saya')
@section('page_section',  'DOSEN')
@section('page_subtitle', 'Informasi akun dan data dosen')

@section('main_content')

@php
$dosen = auth()->user()->dosen;
$user  = auth()->user();
@endphp

@if(session('success'))
<div style="background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.25);border-radius:10px;
            padding:12px 16px;margin-bottom:16px;font-size:13px;color:#065f46;display:flex;align-items:center;gap:8px">
    <i class="fas fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="row g-3">

    {{-- ── KARTU IDENTITAS ─────────────────────────────── --}}
    <div class="col-md-4">
        <div class="sima-card h-100">
            <div class="sima-card__body" style="display:flex;flex-direction:column;align-items:center;padding:32px 20px;text-align:center">

                {{-- Avatar --}}
                <div style="width:72px;height:72px;border-radius:18px;
                            background:linear-gradient(135deg,var(--c-accent),var(--c-blue));
                            display:flex;align-items:center;justify-content:center;
                            font-size:28px;font-weight:700;color:#fff;margin-bottom:16px;
                            box-shadow:0 4px 16px rgba(var(--c-accent-rgb),.25)">
                    {{ strtoupper(substr($dosen->nama ?? $user->email, 0, 1)) }}
                </div>

                <div style="font-size:17px;font-weight:700;color:var(--c-text-1);margin-bottom:4px">
                    {{ $dosen->nama ?? '—' }}
                </div>
                <div style="font-size:12px;color:var(--c-text-3);margin-bottom:14px">
                    {{ $user->email }}
                </div>

                <span class="sima-badge {{ $user->status === 'active' ? 'sima-badge--green' : 'sima-badge--red' }}">
                    <i class="fas fa-circle" style="font-size:7px"></i>
                    {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>

                <div style="margin-top:20px;width:100%;border-top:1px solid var(--c-border);padding-top:16px;
                            display:flex;flex-direction:column;gap:10px;text-align:left">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px">
                        <span style="color:var(--c-text-3)"><i class="fas fa-id-badge me-1"></i> NIDN</span>
                        <span style="font-family:var(--f-mono);font-weight:600;color:var(--c-text-1)">{{ $dosen->nidn ?? '—' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12.5px">
                        <span style="color:var(--c-text-3)"><i class="fas fa-tag me-1"></i> Kode Dosen</span>
                        <span style="font-family:var(--f-mono);font-weight:600;color:var(--c-text-1)">{{ $dosen->kodeDos ?? '—' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:12.5px">
                        <span style="color:var(--c-text-3)"><i class="fas fa-university me-1"></i> Jurusan</span>
                        <span style="font-weight:600;color:var(--c-text-1);text-align:right;max-width:120px">
                            {{ $user->jurusan->namaJurusan ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FORM UPDATE ──────────────────────────────────── --}}
    <div class="col-md-8">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Edit Profil</h5>
                    <div class="sima-card__subtitle">Perbarui nama dan password akun Anda</div>
                </div>
            </div>
            <div class="sima-card__body">

                @if($errors->any())
                <div style="background:rgba(220,38,38,.07);border:1px solid rgba(220,38,38,.2);
                            border-radius:10px;padding:12px 16px;margin-bottom:16px">
                    @foreach($errors->all() as $e)
                    <div style="font-size:12.5px;color:#b91c1c">· {{ $e }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('dosen.profil.update') }}">
                    @csrf
                    @method('PATCH')

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                        <div>
                            <label class="sima-label">Nama Lengkap <span style="color:var(--c-red)">*</span></label>
                            <input type="text" name="nama" value="{{ old('nama', $dosen->nama ?? '') }}"
                                   class="sima-input" required>
                        </div>
                        <div>
                            <label class="sima-label">NIDN</label>
                            <input type="text" value="{{ $dosen->nidn ?? '—' }}" class="sima-input"
                                   disabled style="background:var(--c-bg);color:var(--c-text-3)">
                        </div>
                    </div>

                    <div style="border-top:1px solid var(--c-border);padding-top:16px;margin-bottom:16px">
                        <div style="font-size:12px;font-weight:600;color:var(--c-text-3);
                                    text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">
                            Ganti Password (kosongkan jika tidak ingin mengubah)
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                            <div>
                                <label class="sima-label">Password Baru</label>
                                <input type="password" name="password" class="sima-input"
                                       placeholder="Min. 8 karakter" autocomplete="new-password">
                            </div>
                            <div>
                                <label class="sima-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="sima-input"
                                       placeholder="Ulangi password baru" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="sima-btn">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
