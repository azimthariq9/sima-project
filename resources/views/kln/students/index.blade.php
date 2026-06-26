@extends('layouts.sima')

@section('page_title',    'Students & Lecturers')
@section('page_section',  'KERJA SAMA LUAR NEGERI')
@section('page_subtitle', 'Data mahasiswa dan dosen per jurusan')

@section('main_content')

{{-- ── STAT CARDS ─────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--blue">
            <div class="sima-stat__icon sima-stat__icon--blue"><i class="fas fa-user-graduate"></i></div>
            <div class="sima-stat__label">Total Mahasiswa</div>
            <div class="sima-stat__value">{{ $totalMahasiswa }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--green">
            <div class="sima-stat__icon sima-stat__icon--green"><i class="fas fa-chalkboard-teacher"></i></div>
            <div class="sima-stat__label">Total Dosen</div>
            <div class="sima-stat__value">{{ $totalDosen }}</div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="sima-stat sima-stat--purple">
            <div class="sima-stat__icon sima-stat__icon--purple"><i class="fas fa-university"></i></div>
            <div class="sima-stat__label">Jurusan Aktif</div>
            <div class="sima-stat__value">{{ $totalJurusan }}</div>
        </div>
    </div>
</div>

{{-- ── MAHASISWA TABLE ──────────────────────────────── --}}
<div class="sima-card mb-4">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Mahasiswa</h5>
        </div>
        <form method="GET" action="{{ route('kln.students.page') }}" id="frmM"
              style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            {{-- preserve dosen filter --}}
            @if($searchD)  <input type="hidden" name="search_d"  value="{{ $searchD }}"> @endif
            @if($jurusanD) <input type="hidden" name="jurusan_d" value="{{ $jurusanD }}"> @endif

            <select name="jurusan_m" class="sima-input" style="width:150px;" onchange="this.form.submit()">
                <option value="">Semua Jurusan</option>
                @foreach($jurusan as $j)
                    <option value="{{ $j->id }}" {{ $jurusanM == $j->id ? 'selected' : '' }}>{{ $j->namaJurusan }}</option>
                @endforeach
            </select>
            <select name="tipe_m" class="sima-input" style="width:175px;" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="Beasiswa TIAS"       {{ $tipeMhs === 'Beasiswa TIAS'       ? 'selected' : '' }}>Beasiswa TIAS</option>
                <option value="Beasiswa KNB"        {{ $tipeMhs === 'Beasiswa KNB'        ? 'selected' : '' }}>Beasiswa KNB</option>
                <option value="Beasiswa Gunadarma"  {{ $tipeMhs === 'Beasiswa Gunadarma'  ? 'selected' : '' }}>Beasiswa Gunadarma</option>
                <option value="Internasional Mandiri" {{ $tipeMhs === 'Internasional Mandiri' ? 'selected' : '' }}>Internasional Mandiri</option>
                <option value="Short Course (3 Bulan)" {{ $tipeMhs === 'Short Course (3 Bulan)' ? 'selected' : '' }}>Short Course (3 Bulan)</option>
            </select>
            <select name="dok_status" class="sima-input" style="width:145px;" onchange="this.form.submit()">
                <option value="">Semua Status Dok</option>
                <option value="belum_ada" {{ $dokStatus === 'belum_ada' ? 'selected' : '' }}>Belum Ada</option>
                <option value="expired"   {{ $dokStatus === 'expired'   ? 'selected' : '' }}>Expired</option>
                <option value="pending"   {{ $dokStatus === 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="warning"   {{ $dokStatus === 'warning'   ? 'selected' : '' }}>Warning</option>
                <option value="aman"      {{ $dokStatus === 'aman'      ? 'selected' : '' }}>Aman</option>
            </select>
            <input type="text" name="search_m" value="{{ $searchM }}" class="sima-input" style="width:150px;"
                   placeholder="Cari nama / NPM...">
            <button type="submit" class="sima-btn sima-btn--outline"><i class="fas fa-search"></i></button>
            @if($searchM || $jurusanM || $dokStatus || $tipeMhs)
            <a href="{{ route('kln.students.page', array_filter(['search_d'=>$searchD,'jurusan_d'=>$jurusanD])) }}"
               class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="sima-table">
            @php
            $tipeBadgeMap = [
                'Beasiswa TIAS'        => 'sima-badge--blue',
                'Beasiswa KNB'         => 'sima-badge--green',
                'Beasiswa Gunadarma'   => 'sima-badge--purple',
                'Internasional Mandiri'=> 'sima-badge--teal',
                'Short Course (3 Bulan)' => 'sima-badge--amber',
            ];
            @endphp
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NPM</th>
                    <th>Jurusan</th>
                    <th>Tipe</th>
                    <th>Akun</th>
                    <th>Status Dokumen</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswaList as $idx => $m)
                @php
                    $docLevel = $m->doc_expiry_level;
                    if ($docLevel === null)  { $docBadge = ''; $docStyle = 'background:var(--c-border);color:var(--c-text-3);'; $docLabel = 'Belum Ada'; $docIcon = 'fa-minus-circle'; }
                    elseif ($docLevel == 1) { $docBadge = 'sima-badge--red';    $docStyle = ''; $docLabel = 'Expired'; $docIcon = 'fa-exclamation-circle'; }
                    elseif ($docLevel == 2) { $docBadge = 'sima-badge--blue';   $docStyle = ''; $docLabel = 'Pending'; $docIcon = 'fa-clock'; }
                    elseif ($docLevel == 3) { $docBadge = 'sima-badge--amber';  $docStyle = ''; $docLabel = 'Warning'; $docIcon = 'fa-exclamation-triangle'; }
                    else                    { $docBadge = 'sima-badge--green';  $docStyle = ''; $docLabel = 'Aman';    $docIcon = 'fa-check-circle'; }
                    $akunBadge = $m->status === 'active' ? 'sima-badge--green' : 'sima-badge--red';
                    $akunLabel = $m->status === 'active' ? 'Aktif' : 'Nonaktif';
                    $tipeCls   = $tipeBadgeMap[$m->tipeMahasiswa ?? ''] ?? '';
                @endphp
                <tr>
                    <td>{{ $mahasiswaList->firstItem() + $loop->index }}</td>
                    <td>{{ $m->nama }}</td>
                    <td><span style="font-family:var(--f-mono);font-size:13px;">{{ $m->identifier ?? '-' }}</span></td>
                    <td>{{ $m->namaJurusan ?? '-' }}</td>
                    <td>
                        @if($m->tipeMahasiswa)
                            <span class="sima-badge {{ $tipeCls }}" style="white-space:nowrap">{{ $m->tipeMahasiswa }}</span>
                        @else
                            <span style="font-size:12px;color:var(--c-text-3)">—</span>
                        @endif
                    </td>
                    <td><span class="sima-badge {{ $akunBadge }}">{{ $akunLabel }}</span></td>
                    <td>
                        <span class="sima-badge {{ $docBadge }}" style="{{ $docStyle }}">
                            <i class="fas {{ $docIcon }} me-1"></i>{{ $docLabel }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('kln.students.mahasiswa', $m->id) }}" class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data mahasiswa.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mahasiswaList->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $mahasiswaList->links('vendor.pagination.sima') }}
    </div>
    @endif
</div>

{{-- ── DOSEN TABLE ──────────────────────────────────── --}}
<div class="sima-card">
    <div class="sima-card__header">
        <div>
            <h5 class="sima-card__title">Dosen</h5>
        </div>
        <form method="GET" action="{{ route('kln.students.page') }}"
              style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            {{-- preserve mahasiswa filter --}}
            @if($searchM)   <input type="hidden" name="search_m"   value="{{ $searchM }}"> @endif
            @if($jurusanM)  <input type="hidden" name="jurusan_m"  value="{{ $jurusanM }}"> @endif
            @if($dokStatus) <input type="hidden" name="dok_status" value="{{ $dokStatus }}"> @endif
            @if($tipeMhs)   <input type="hidden" name="tipe_m"     value="{{ $tipeMhs }}">  @endif

            <select name="jurusan_d" class="sima-input" style="width:150px;" onchange="this.form.submit()">
                <option value="">Semua Jurusan</option>
                @foreach($jurusan as $j)
                    <option value="{{ $j->id }}" {{ $jurusanD == $j->id ? 'selected' : '' }}>{{ $j->namaJurusan }}</option>
                @endforeach
            </select>
            <input type="text" name="search_d" value="{{ $searchD }}" class="sima-input" style="width:150px;"
                   placeholder="Cari nama...">
            <button type="submit" class="sima-btn sima-btn--outline"><i class="fas fa-search"></i></button>
            @if($searchD || $jurusanD)
            <a href="{{ route('kln.students.page', array_filter(['search_m'=>$searchM,'jurusan_m'=>$jurusanM,'dok_status'=>$dokStatus,'tipe_m'=>$tipeMhs])) }}"
               class="sima-btn sima-btn--outline"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="sima-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>NIDN</th>
                    <th>Jurusan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dosenList as $idx => $d)
                @php
                    $badgeSts = $d->status === 'active' ? 'sima-badge--green' : 'sima-badge--red';
                    $labelSts = $d->status === 'active' ? 'Aktif' : 'Nonaktif';
                @endphp
                <tr>
                    <td>{{ $dosenList->firstItem() + $loop->index }}</td>
                    <td>{{ $d->nama }}</td>
                    <td><span style="font-family:var(--f-mono);font-size:13px;">{{ $d->identifier ?? '-' }}</span></td>
                    <td>{{ $d->namaJurusan ?? '-' }}</td>
                    <td><span class="sima-badge {{ $badgeSts }}">{{ $labelSts }}</span></td>
                    <td>
                        <a href="{{ route('kln.students.dosen', $d->id) }}" class="sima-btn sima-btn--outline sima-btn--sm">
                            <i class="fas fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data dosen.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($dosenList->hasPages())
    <div style="padding:14px 20px;border-top:1px solid var(--c-border);">
        {{ $dosenList->links('vendor.pagination.sima') }}
    </div>
    @endif
</div>

@endsection
