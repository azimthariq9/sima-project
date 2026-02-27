@extends('layouts.sima')

@section('page_title',    'Pengumuman KLN')
@section('page_section',  'KLN')
@section('page_subtitle', 'Kelola pengumuman untuk mahasiswa asing')

@section('main_content')

<div class="row g-3">

    {{-- Form buat pengumuman baru --}}
    <div class="col-md-5 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Buat Pengumuman Baru</h5>
                    <div class="sima-card__subtitle">Akan dikirim ke mahasiswa sesuai target</div>
                </div>
            </div>
            <div class="sima-card__body">
                <form method="POST" action="{{ route('kln.announcement.store') }}">
                    @csrf

                    @if(session('success'))
                    <div class="sima-alert sima-alert--green" style="margin-bottom:16px">
                        <i class="fas fa-circle-check sima-alert__icon"></i>
                        <div class="sima-alert__text">{{ session('success') }}</div>
                    </div>
                    @endif

                    <div style="margin-bottom:14px">
                        <label class="sima-label">Judul <span style="color:var(--c-red)">*</span></label>
                        <input type="text" name="judul" class="sima-input"
                               value="{{ old('judul') }}"
                               placeholder="Contoh: Pengingat Perpanjangan KITAS Maret 2026"
                               required>
                        @error('judul') <div style="font-size:12px;color:var(--c-red);margin-top:4px">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-bottom:14px">
                        <label class="sima-label">Isi Pengumuman <span style="color:var(--c-red)">*</span></label>
                        <textarea name="isi" class="sima-input" rows="5"
                                  placeholder="Tulis isi pengumuman lengkap di sini…"
                                  required style="resize:vertical">{{ old('isi') }}</textarea>
                    </div>

                    <div style="margin-bottom:14px">
                        <label class="sima-label">Target Penerima</label>
                        <select name="target" class="sima-input">
                            <option value="all">🌍 Semua Mahasiswa Asing</option>
                            <option value="uzbekistan">🇺🇿 Uzbekistan (38 mhs)</option>
                            <option value="vietnam">🇻🇳 Vietnam (22 mhs)</option>
                            <option value="korea">🇰🇷 Korea Selatan (18 mhs)</option>
                            <option value="tajikistan">🇹🇯 Tajikistan (15 mhs)</option>
                            <option value="senegal">🇸🇳 Senegal (12 mhs)</option>
                            <option value="malaysia">🇲🇾 Malaysia (9 mhs)</option>
                        </select>
                    </div>

                    <div style="margin-bottom:14px">
                        <label class="sima-label">Sumber / Pengirim</label>
                        <select name="sumber" class="sima-input">
                            <option value="KLN">KLN — Kantor Layanan Internasional</option>
                            <option value="Jurusan">Jurusan</option>
                            <option value="BIPA">BIPA</option>
                            <option value="Rektorat">Rektorat</option>
                        </select>
                    </div>

                    <div style="margin-bottom:18px">
                        <label class="sima-label">Prioritas</label>
                        <div style="display:flex;gap:10px">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;color:var(--c-text-2)">
                                <input type="radio" name="is_penting" value="0" checked style="accent-color:var(--c-accent)">
                                ℹ️ Informasi
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13.5px;color:var(--c-text-2)">
                                <input type="radio" name="is_penting" value="1" style="accent-color:var(--c-amber)">
                                ⚠️ Penting
                            </label>
                        </div>
                    </div>

                    <div style="display:flex;gap:8px">
                        <button type="submit" class="sima-btn sima-btn--gold">
                            <i class="fas fa-paper-plane"></i> Kirim Sekarang
                        </button>
                        <button type="reset" class="sima-btn sima-btn--outline">
                            <i class="fas fa-rotate-left"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar pengumuman yang sudah ada --}}
    <div class="col-md-7 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Riwayat Pengumuman</h5>
                    <div class="sima-card__subtitle">Semua pengumuman yang pernah dikirim</div>
                </div>
            </div>
            <div class="sima-card__body">

                @forelse($announcements ?? [] as $ann)
                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">{{ $ann->judul }}</div>
                        <div style="display:flex;gap:5px;flex-shrink:0;margin-left:10px">
                            <span class="sima-badge sima-badge--teal">{{ $ann->sumber }}</span>
                            @if($ann->is_penting)
                                <span class="sima-badge sima-badge--amber">Penting</span>
                            @endif
                        </div>
                    </div>
                    <div class="sima-announce__body">{{ Str::limit($ann->isi, 120) }}</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> {{ $ann->target === 'all' ? 'Semua mahasiswa' : $ann->target }}
                        &nbsp;·&nbsp; <i class="fas fa-clock"></i> {{ $ann->created_at->diffForHumans() }}
                    </div>
                </div>
                @empty
                @foreach([
                    ['title'=>'Orientasi Mahasiswa Baru Semester Genap','sumber'=>'KLN','body'=>'Orientasi dilaksanakan Senin, 24 Februari 2026 pukul 08.00 WIB di Aula Utama. Wajib hadir membawa dokumen identitas.','target'=>'Semua mahasiswa','time'=>'2 jam lalu','penting'=>true],
                    ['title'=>'Pengingat Perpanjangan KITAS — Maret 2026','sumber'=>'KLN','body'=>'Bagi mahasiswa yang KITAS-nya berakhir pada Maret 2026, segera hubungi KLN paling lambat 2 minggu sebelum tanggal expired.','target'=>'Semua mahasiswa','time'=>'1 hari lalu','penting'=>true],
                    ['title'=>'Jadwal UTS Semester Genap 2025/2026','sumber'=>'Jurusan','body'=>'Ujian Tengah Semester dilaksanakan 10–21 Maret 2026. Kartu ujian diambil mulai 3 Maret.','target'=>'Semua mahasiswa','time'=>'2 hari lalu','penting'=>false],
                    ['title'=>'Kelas BIPA B2 Dibuka — Kuota Terbatas','sumber'=>'BIPA','body'=>'Pendaftaran kelas BIPA B2 dibuka mulai 20 Februari 2026. Kuota terbatas 20 mahasiswa.','target'=>'Semua mahasiswa','time'=>'5 hari lalu','penting'=>false],
                ] as $dummy)
                <div class="sima-announce">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                        <div class="sima-announce__title">{{ $dummy['title'] }}</div>
                        <div style="display:flex;gap:5px;flex-shrink:0;margin-left:10px">
                            <span class="sima-badge sima-badge--teal">{{ $dummy['sumber'] }}</span>
                            @if($dummy['penting'])
                                <span class="sima-badge sima-badge--amber">Penting</span>
                            @endif
                        </div>
                    </div>
                    <div class="sima-announce__body">{{ $dummy['body'] }}</div>
                    <div class="sima-announce__meta">
                        <i class="fas fa-users"></i> {{ $dummy['target'] }}
                        &nbsp;·&nbsp; <i class="fas fa-clock"></i> {{ $dummy['time'] }}
                        <button class="sima-btn sima-btn--outline sima-btn--sm" style="margin-left:auto;font-size:11px;padding:3px 9px">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                @endforeach
                @endforelse

            </div>
        </div>
    </div>

</div>
@endsection
