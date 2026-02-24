@extends('layouts.sima')

@section('page_title',   'Pengumuman')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Informasi terbaru dari KLN, Jurusan & BIPA')

@section('main_content')

{{-- Filter bar --}}
<div class="sima-card sima-fade mb-4" style="padding:14px 20px">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <span style="font-size:13px;font-weight:600;color:var(--c-text-2)">Filter:</span>
        <a href="{{ route('mahasiswa.announcement') }}"
           class="sima-badge {{ !request('sumber') ? 'sima-badge--blue' : 'sima-badge--grey' }}"
           style="padding:5px 13px;cursor:pointer;font-size:12px">Semua</a>
        <a href="{{ route('mahasiswa.announcement', ['sumber' => 'KLN']) }}"
           class="sima-badge {{ request('sumber') === 'KLN' ? 'sima-badge--teal' : 'sima-badge--grey' }}"
           style="padding:5px 13px;cursor:pointer;font-size:12px">KLN</a>
        <a href="{{ route('mahasiswa.announcement', ['sumber' => 'Jurusan']) }}"
           class="sima-badge {{ request('sumber') === 'Jurusan' ? 'sima-badge--purple' : 'sima-badge--grey' }}"
           style="padding:5px 13px;cursor:pointer;font-size:12px">Jurusan</a>
        <a href="{{ route('mahasiswa.announcement', ['sumber' => 'BIPA']) }}"
           class="sima-badge {{ request('sumber') === 'BIPA' ? 'sima-badge--amber' : 'sima-badge--grey' }}"
           style="padding:5px 13px;cursor:pointer;font-size:12px">BIPA</a>
        <div style="margin-left:auto;display:flex;align-items:center;gap:8px">
            <input type="text" class="sima-input" placeholder="Cari pengumuman…"
                   style="width:220px;font-size:13px;padding:7px 13px">
        </div>
    </div>
</div>

<div class="sima-card sima-fade sima-fade--1">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Semua Pengumuman</h5>
            <div class="sima-card__subtitle">Dari KLN, Jurusan &amp; BIPA</div>
        </div>
    </div>
    <div class="sima-card__body">

        @forelse($announcements ?? [] as $ann)
        <div class="sima-announce" onclick="window.location='{{ route('mahasiswa.announcement.show', $ann->id) }}'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                <div class="sima-announce__title">{{ $ann->icon ?? '📋' }} {{ $ann->judul }}</div>
                <div style="display:flex;gap:6px;flex-shrink:0;margin-left:10px">
                    <span class="sima-badge sima-badge--{{ $ann->badge_color ?? 'blue' }}">{{ $ann->sumber }}</span>
                    @if($ann->is_penting)
                        <span class="sima-badge sima-badge--amber">Penting</span>
                    @endif
                </div>
            </div>
            <div class="sima-announce__body">{{ Str::limit($ann->isi, 160) }}</div>
            <div class="sima-announce__meta">
                <i class="fas fa-clock"></i> {{ $ann->created_at->diffForHumans() }}
                <a href="{{ route('mahasiswa.announcement.show', $ann->id) }}"
                   style="margin-left:auto;font-size:12px;color:var(--c-accent);font-weight:600"
                   onclick="event.stopPropagation()">Selengkapnya →</a>
            </div>
        </div>
        @empty
        {{-- Dummy --}}
        @foreach([
            ['icon'=>'📋','title'=>'Orientasi Mahasiswa Baru Semester Genap','sumber'=>'KLN','badge'=>'teal','body'=>'Orientasi dilaksanakan Senin, 24 Februari 2026 pukul 08.00 WIB di Aula Utama Gedung Rektorat. Seluruh mahasiswa asing wajib hadir membawa dokumen identitas asli dan KTM.','time'=>'2 jam lalu','penting'=>true],
            ['icon'=>'🎓','title'=>'Jadwal UTS Semester Genap 2025/2026','sumber'=>'Jurusan','badge'=>'purple','body'=>'Ujian Tengah Semester dilaksanakan 10–21 Maret 2026. Kartu ujian diambil mulai 3 Maret di bagian akademik dengan menunjukkan KTM asli.','time'=>'1 hari lalu','penting'=>false],
            ['icon'=>'🌐','title'=>'Kelas BIPA B2 Dibuka — Kuota Terbatas','sumber'=>'BIPA','badge'=>'amber','body'=>'Pendaftaran kelas BIPA B2 dibuka mulai 20 Februari 2026 melalui portal SIMA. Kuota terbatas 20 mahasiswa, seleksi 22 Februari 2026.','time'=>'3 hari lalu','penting'=>false],
            ['icon'=>'📄','title'=>'Pembaruan Prosedur Pengajuan KITAS','sumber'=>'KLN','badge'=>'teal','body'=>'Mulai 1 Maret 2026, pengajuan KITAS wajib disertai surat sponsor dari universitas. Hubungi KLN untuk informasi lebih lanjut.','time'=>'5 hari lalu','penting'=>true],
        ] as $dummy)
        <div class="sima-announce">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:5px">
                <div class="sima-announce__title">{{ $dummy['icon'] }} {{ $dummy['title'] }}</div>
                <div style="display:flex;gap:6px;flex-shrink:0;margin-left:10px">
                    <span class="sima-badge sima-badge--{{ $dummy['badge'] }}">{{ $dummy['sumber'] }}</span>
                    @if($dummy['penting'])
                        <span class="sima-badge sima-badge--amber">Penting</span>
                    @endif
                </div>
            </div>
            <div class="sima-announce__body">{{ $dummy['body'] }}</div>
            <div class="sima-announce__meta">
                <i class="fas fa-clock"></i> {{ $dummy['time'] }}
                <span class="sima-badge sima-badge--grey" style="margin-left:4px">{{ $dummy['penting'] ? 'Penting' : 'Informasi' }}</span>
            </div>
        </div>
        @endforeach
        @endforelse

    </div>
</div>
@endsection
