@extends('layouts.sima')

@section('page_title',    'Dokumen Mahasiswa')
@section('page_section',  'ADMIN JURUSAN')
@section('page_subtitle', 'Daftar dokumen penting yang diupload mahasiswa')

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
            <h5 class="sima-card__title">Dokumen Mahasiswa</h5>
            <div class="sima-card__subtitle">Dokumen penting yang diupload oleh mahasiswa di jurusan ini</div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div style="padding:12px 20px;border-bottom:1px solid var(--c-border-soft)">
        <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
            <input type="text" name="search" value="{{ $search }}" placeholder="Nama/NPM/nomor dokumen…"
                   class="sima-input" style="width:260px;font-size:13px">
            <select name="filter" class="sima-input" style="width:150px;font-size:13px" onchange="this.form.submit()">
                <option value="">— Semua Status —</option>
                <option value="pending"  {{ $filter === 'pending'  ? 'selected' : '' }}>Menunggu</option>
                <option value="approved" {{ $filter === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="sima-btn sima-btn--sm sima-btn--outline">
                <i class="fas fa-search"></i> Cari
            </button>
            @if($search || $filter)
                <a href="{{ route('jurusan.dokumen.index') }}" class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Reset
                </a>
            @endif
        </form>
    </div>

    @if($dokumen->isEmpty())
        <div class="sima-card__body" style="text-align:center;padding:48px 20px;color:var(--c-text-3)">
            <i class="fas fa-folder-open" style="font-size:36px;opacity:.3;display:block;margin-bottom:12px"></i>
            <div style="font-size:14px;font-weight:500;color:var(--c-text-2)">
                {{ $search || $filter ? 'Tidak ada dokumen yang cocok' : 'Belum ada dokumen yang diupload' }}
            </div>
        </div>
    @else
        <div class="sima-card__body" style="padding:0">
            <table class="sima-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Mahasiswa</th>
                        <th>Dokumen</th>
                        <th>Penerbit</th>
                        <th>Berlaku s/d</th>
                        <th style="width:110px">Status</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dokumen as $dok)
                    @php
                        $stMap = [
                            'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Menunggu'],
                            'approved' => ['cls'=>'sima-badge--green', 'label'=>'Disetujui'],
                            'rejected' => ['cls'=>'sima-badge--red',   'label'=>'Ditolak'],
                        ];
                        $st = $stMap[$dok->status] ?? $stMap['pending'];
                        $today = now()->toDateString();
                        $soon  = now()->addDays(30)->toDateString();
                        $exp   = $dok->tglKdlwrs ?? null;
                        $expClr = !$exp ? 'var(--c-text-3)' : ($exp < $today ? '#ef4444' : ($exp <= $soon ? '#f59e0b' : 'var(--c-text-1)'));
                    @endphp
                    <tr>
                        <td style="color:var(--c-text-3);font-size:12px">
                            {{ $dokumen->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13.5px">{{ $dok->nama_mahasiswa }}</div>
                            <div style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">{{ $dok->npm }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;font-size:13px">
                                {{ $dok->namaDkmn ?? str_replace('_', ' ', $dok->tipeDkmn) }}
                            </div>
                            <div style="font-size:11.5px;color:var(--c-text-3)">No. {{ $dok->noDkmn ?? '—' }}</div>
                        </td>
                        <td style="font-size:13px">{{ $dok->penerbit ?? '—' }}</td>
                        <td style="font-family:var(--f-mono);font-size:12px;color:{{ $expClr }}">
                            {{ $exp ? \Carbon\Carbon::parse($exp)->format('d M Y') : '—' }}
                        </td>
                        <td>
                            <span class="sima-badge {{ $st['cls'] }}" style="font-size:11px">{{ $st['label'] }}</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;align-items:center">
                                <a href="{{ route('jurusan.dokumen.file', $dok->id) }}"
                                   class="sima-btn sima-btn--sm sima-btn--outline"
                                   style="font-size:11px;padding:4px 10px" title="Unduh">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if($dok->status === 'pending')
                                <form method="POST" action="{{ route('jurusan.dokumen.status', $dok->id) }}" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="sima-btn sima-btn--sm"
                                            style="font-size:11px;padding:4px 10px;background:rgba(5,150,105,.08);color:#059669;border:1px solid rgba(5,150,105,.2)"
                                            onclick="return confirm('Setujui dokumen ini?')" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('jurusan.dokumen.status', $dok->id) }}" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="sima-btn sima-btn--sm"
                                            style="font-size:11px;padding:4px 10px;background:rgba(220,38,38,.08);color:#dc2626;border:1px solid rgba(220,38,38,.2)"
                                            onclick="return confirm('Tolak dokumen ini?')" title="Tolak">
                                        <i class="fas fa-xmark"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="padding:14px 20px">
            {{ $dokumen->links('vendor.pagination.sima') }}
        </div>
    @endif
</div>

@endsection
