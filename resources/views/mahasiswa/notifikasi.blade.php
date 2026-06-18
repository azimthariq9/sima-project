@extends('layouts.sima')

@section('page_title',   'Notifikasi')
@section('page_section', 'Mahasiswa')
@section('page_subtitle','Informasi & aktivitas terbaru akun Anda')

@section('main_content')

<div class="row g-3">
    <div class="col-md-8 sima-fade">
        <div class="sima-card">
            <div class="sima-card__header">
                <div>
                    <h5 class="sima-card__title">Semua Notifikasi</h5>
                    <div class="sima-card__subtitle">{{ $unreadNotifCount ?? 0 }} belum dibaca</div>
                </div>
                <button class="sima-btn sima-btn--outline sima-btn--sm" onclick="markAllRead()">
                    <i class="fas fa-check-double"></i> Tandai Semua Dibaca
                </button>
            </div>

            @php
            // Transform data dari paginator DB ke format array yang dipakai view
            if (!empty($notifikasi) && $notifikasi->count() > 0) {
                $notifs = $notifikasi->map(fn($n) => [
                    'ic'     => 'fa-bell',
                    'cl'     => '#2563EB',
                    'bg'     => '#EFF6FF',
                    'text'   => e($n->subject) . ($n->message ? '<br><span style="font-size:12px;color:var(--c-text-3)">' . e($n->message) . '</span>' : ''),
                    'time'   => \Carbon\Carbon::parse($n->received_at)->diffForHumans(),
                    'unread' => !$n->is_read,
                ]);
            } else {
                $notifs = [];
            }
            @endphp

            <div id="notif-list">
                @forelse($notifs as $n)
                @php $nr = is_array($n) ? $n : (array) $n; @endphp
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 20px;
                            border-bottom:1px solid var(--c-border-soft);
                            background:{{ ($nr['unread'] ?? false) ? 'rgba(108,143,255,.03)' : 'transparent' }};
                            transition:background .13s;cursor:pointer"
                     onmouseover="this.style.background='var(--c-surface-2)'"
                     onmouseout="this.style.background='{{ ($nr['unread'] ?? false) ? 'rgba(108,143,255,.03)' : 'transparent' }}'">
                    <div style="width:38px;height:38px;border-radius:11px;background:{{ $nr['bg'] }};color:{{ $nr['cl'] }};display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0">
                        <i class="fas {{ $nr['ic'] }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;padding-top:2px">
                        <div style="font-size:13.5px;color:var(--c-text-1);line-height:1.5;{{ ($nr['unread'] ?? false) ? 'font-weight:500' : '' }}">{!! $nr['text'] !!}</div>
                        <div style="font-family:var(--f-mono);font-size:11px;color:var(--c-text-3);margin-top:4px"><i class="fas fa-clock" style="font-size:10px"></i> {{ $nr['time'] }}</div>
                    </div>
                    @if($nr['unread'] ?? false)
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--c-accent);flex-shrink:0;margin-top:6px"></div>
                    @endif
                </div>
                @empty
                <div style="padding:40px 0;text-align:center;color:var(--c-text-3)">
                    <i class="fas fa-bell-slash" style="font-size:28px;margin-bottom:10px;opacity:.3"></i>
                    <div style="font-size:13px">Belum ada notifikasi</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-md-4 sima-fade sima-fade--1">
        <div class="sima-card">
            <div class="sima-card__header">
                <h5 class="sima-card__title">Pengaturan</h5>
            </div>
            <div class="sima-card__body">
                <p style="font-size:13px;color:var(--c-text-3);margin-bottom:16px">Atur jenis notifikasi yang ingin Anda terima.</p>
                @foreach(['Dokumen expired','Pengumuman KLN','Status request','Jadwal kuliah','Perubahan profil'] as $pref)
                <label style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--c-border-soft);cursor:pointer">
                    <span style="font-size:13.5px;color:var(--c-text-2)">{{ $pref }}</span>
                    <input type="checkbox" checked style="width:16px;height:16px;accent-color:var(--c-accent);cursor:pointer">
                </label>
                @endforeach
                <div style="margin-top:16px">
                    <button class="sima-btn sima-btn--sm sima-btn--full" onclick="alert('Preferensi disimpan!')">
                        <i class="fas fa-save"></i> Simpan Preferensi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
function markAllRead() {
    document.querySelectorAll('#notif-list > div').forEach(el => {
        el.style.background = 'transparent';
        const dot = el.querySelector('div[style*="border-radius:50%"]');
        if (dot) dot.remove();
        const txt = el.querySelector('div[style*="font-size:13.5px"]');
        if (txt) txt.style.fontWeight = '400';
    });
}
</script>
@endsection
