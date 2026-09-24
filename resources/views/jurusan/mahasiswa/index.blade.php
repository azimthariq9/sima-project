@extends('layouts.sima')

@section('page_title',    'Students')
@section('page_section',  'DEPARTMENT ADMIN')
@section('page_subtitle', 'Student data in your department')

@section('main_content')

<div class="sima-card sima-fade">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Student List</h5>
            <div class="sima-card__subtitle">Total {{ $mahasiswa->count() }} students</div>
        </div>
    </div>

    <div style="overflow-x:auto">
        <table class="sima-table" data-datatable>
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Class</th>
                    <th style="width:110px">Status</th>
                    <th style="width:70px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($mahasiswa as $u)
                @php
                    $mhs = $u->mahasiswa;
                    $klsList = $mhs && $mhs->kelas ? $mhs->kelas->pluck('kodeKelas')->join(', ') : '—';
                @endphp
                <tr>
                    <td style="color:var(--c-text-3);font-size:12px">
                        {{ $loop->index + 1 }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13.5px">{{ $mhs->nama ?? '(Incomplete)' }}</div>
                        <div style="font-family:var(--f-mono);font-size:11.5px;color:var(--c-text-3)">
                            {{ $mhs->npm ?? '—' }}
                        </div>
                    </td>
                    <td style="font-size:12.5px;color:var(--c-text-2)">{{ $u->email }}</td>
                    <td style="font-size:12.5px">{{ $klsList }}</td>
                    <td>
                        @php
                            $stMap = [
                                'active'   => ['cls'=>'sima-badge--green', 'label'=>'Active'],
                                'inactive' => ['cls'=>'sima-badge--grey',  'label'=>'Inactive'],
                                'pending'  => ['cls'=>'sima-badge--blue',  'label'=>'Pending'],
                            ];
                            $st = $stMap[$u->status] ?? ['cls'=>'sima-badge--grey', 'label'=>$u->status];
                        @endphp
                        <span class="sima-badge {{ $st['cls'] }}" style="font-size:11px">{{ $st['label'] }}</span>
                    </td>
                    <td>
                        <button type="button" class="sima-btn sima-btn--sm sima-btn--outline btn-detail"
                                data-mhs="{{ json_encode([
                                    'nama'     => $mhs->nama ?? '—',
                                    'npm'      => $mhs->npm ?? '—',
                                    'email'    => $u->email,
                                    'noWa'     => $mhs->noWa ?? '—',
                                    'tglLahir' => $mhs->tglLahir ?? '—',
                                    'warNeg'   => $mhs->warNeg ?? '—',
                                    'masaAktif'=> $mhs->masaAktif ?? '—',
                                    'status'   => $u->status,
                                    'kelas'    => $klsList,
                                ]) }}"
                                style="font-size:11.5px;padding:4px 10px">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- ── MODAL DETAIL MAHASISWA (SIMA light theme) ─── --}}
<div id="detailModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);overflow:hidden">

        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div id="dm-nama" style="font-size:16px;font-weight:700;color:var(--c-text-1)"></div>
                <div id="dm-npm" style="font-family:var(--f-mono);font-size:12px;color:var(--c-text-3);margin-top:2px"></div>
            </div>
            <button onclick="document.getElementById('detailModal').style.display='none'"
                    style="width:32px;height:32px;border:1px solid #e2e8f0;border-radius:8px;background:#f8fafc;cursor:pointer;color:#64748b">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div style="padding:20px 24px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Email</div>
                    <div id="dm-email" style="font-size:12.5px;font-weight:500;color:#1e293b;word-break:break-all"></div>
                </div>
                <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">WhatsApp</div>
                    <div id="dm-noWa" style="font-size:12.5px;font-weight:500;color:#1e293b"></div>
                </div>
                <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Date of Birth</div>
                    <div id="dm-tglLahir" style="font-size:12.5px;font-weight:500;color:#1e293b"></div>
                </div>
                <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Nationality</div>
                    <div id="dm-warNeg" style="font-size:12.5px;font-weight:500;color:#1e293b"></div>
                </div>
                <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9">
                    <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Active Period</div>
                    <div id="dm-masaAktif" style="font-size:12.5px;font-weight:500;color:#1e293b"></div>
                </div>
            </div>
            <div style="padding:11px;background:#f8fafc;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:14px">
                <div style="font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:4px">Classes Enrolled</div>
                <div id="dm-kelas" style="font-size:12.5px;font-weight:500;color:#1e293b"></div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div id="dm-status"></div>
                <button onclick="document.getElementById('detailModal').style.display='none'"
                        class="sima-btn sima-btn--sm sima-btn--outline">
                    <i class="fas fa-xmark"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page_js')
<script>
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const d = JSON.parse(this.dataset.mhs);
        document.getElementById('dm-nama').textContent    = d.nama;
        document.getElementById('dm-npm').textContent     = 'NPM: ' + d.npm;
        document.getElementById('dm-email').textContent   = d.email;
        document.getElementById('dm-noWa').textContent    = d.noWa;
        document.getElementById('dm-tglLahir').textContent = d.tglLahir;
        document.getElementById('dm-warNeg').textContent  = d.warNeg;
        document.getElementById('dm-masaAktif').textContent = d.masaAktif && d.masaAktif !== '—' ? d.masaAktif : '—';
        document.getElementById('dm-kelas').textContent   = d.kelas;

        const stMap = {
            active:   {bg:'#ecfdf5',color:'#065f46',label:'Active'},
            inactive: {bg:'#f8fafc',color:'#475569',label:'Inactive'},
            pending:  {bg:'#eff6ff',color:'#1e40af',label:'Pending'},
        };
        const st = stMap[d.status] || stMap.inactive;
        document.getElementById('dm-status').innerHTML =
            `<span style="font-size:12.5px;font-weight:600;padding:4px 12px;border-radius:100px;background:${st.bg};color:${st.color}">${st.label}</span>`;

        document.getElementById('detailModal').style.display = 'flex';
    });
});
</script>
@endsection
