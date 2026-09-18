@extends('layouts.sima')

@section('page_title',    'Create Student')
@section('page_section',  'USERS')
@section('page_subtitle', 'Multi-step wizard to create a new mahasiswa account')

@section('main_content')

<div class="mb-3">
    <a href="{{ route('kln.users.page') }}" class="sima-btn sima-btn--outline sima-btn--sm">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

{{-- ── STEP INDICATOR ───────────────────────────────── --}}
<div style="display:flex;align-items:center;gap:0;margin-bottom:24px;padding:0 8px;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:1;">
        <div id="stepCircle1" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#fff;background:var(--c-accent);transition:all .25s ease;box-shadow:0 2px 8px rgba(108,143,255,.35);">1</div>
        <div style="font-size:11px;font-weight:600;color:var(--c-text-2);text-align:center;white-space:nowrap;">Biodata</div>
    </div>
    <div id="stepLine1" style="flex:1;height:3px;background:var(--c-border);margin:0 -4px;margin-bottom:22px;transition:background .25s ease;"></div>
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:1;">
        <div id="stepCircle2" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--c-text-3);background:var(--c-surface);border:2px solid var(--c-border);transition:all .25s ease;">2</div>
        <div style="font-size:11px;font-weight:600;color:var(--c-text-3);text-align:center;white-space:nowrap;">External Docs</div>
    </div>
    <div id="stepLine2" style="flex:1;height:3px;background:var(--c-border);margin:0 -4px;margin-bottom:22px;transition:background .25s ease;"></div>
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:1;">
        <div id="stepCircle3" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--c-text-3);background:var(--c-surface);border:2px solid var(--c-border);transition:all .25s ease;">3</div>
        <div style="font-size:11px;font-weight:600;color:var(--c-text-3);text-align:center;white-space:nowrap;">Internal Docs</div>
    </div>
    <div id="stepLine3" style="flex:1;height:3px;background:var(--c-border);margin:0 -4px;margin-bottom:22px;transition:background .25s ease;"></div>
    <div style="display:flex;flex-direction:column;align-items:center;gap:6px;position:relative;z-index:1;">
        <div id="stepCircle4" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--c-text-3);background:var(--c-surface);border:2px solid var(--c-border);transition:all .25s ease;">4</div>
        <div style="font-size:11px;font-weight:600;color:var(--c-text-3);text-align:center;white-space:nowrap;">Checker</div>
    </div>
</div>

<input type="hidden" id="currentUserId" value="">

{{-- ═══════════════════════════════════════════════════
     STEP 1 — BIODATA
     ═══════════════════════════════════════════════════ --}}
<div id="step1">
    <div class="sima-card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
            <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Step 1 — Biodata</h5>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">Personal information and account details</div>
        </div>
        <div style="padding:24px;">
            <form id="step1Form" enctype="multipart/form-data">
                <div style="display:flex;flex-direction:column;gap:20px;">

                    {{-- Name + Tahun Masuk --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Name <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="text" name="nama" class="sima-input mt-1" placeholder="Full name" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Enrollment Year
                            </label>
                            <input type="text" name="tahunMasuk" class="sima-input mt-1" placeholder="PTA 2026/2027">
                        </div>
                    </div>

                    {{-- Email + Password --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Email <span style="color:var(--c-red)">*</span>
                            </label>
                            <input type="email" name="email" class="sima-input mt-1" placeholder="email@institution.ac.id" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Password
                                <span style="font-weight:400;color:var(--c-text-3);font-size:11px;">(optional — leave empty for OTP)</span>
                            </label>
                            <input type="password" name="password" class="sima-input mt-1" placeholder="Min. 6 characters, or leave empty">
                        </div>
                    </div>

                    {{-- WA Number + Emergency Number --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                WA Number
                            </label>
                            <input type="text" name="noWa" class="sima-input mt-1" placeholder="+62 812 3456 7890">
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Emergency Number
                            </label>
                            <input type="text" name="noDarurat" class="sima-input mt-1" placeholder="+62 812 3456 7890">
                        </div>
                    </div>

                    {{-- Birth Date + Country --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Birth Date
                            </label>
                            <input type="date" name="tglLahir" class="sima-input mt-1">
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Country
                            </label>
                            <select name="warNeg" class="sima-input mt-1">
                                <option value="">— Select Country —</option>
                                @php
                                    $countries = [
                                        'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia',
                                        'Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin',
                                        'Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi',
                                        'Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia',
                                        'Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica',
                                        'Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini',
                                        'Ethiopia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada',
                                        'Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia',
                                        'Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati',
                                        'Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania',
                                        'Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania',
                                        'Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique',
                                        'Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria',
                                        'North Korea','North Macedonia','Norway','Oman','Pakistan','Palau','Palestine','Panama','Papua New Guinea',
                                        'Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda',
                                        'Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino',
                                        'Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore',
                                        'Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain',
                                        'Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand',
                                        'Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda',
                                        'Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay','Uzbekistan','Vanuatu',
                                        'Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe',
                                    ];
                                @endphp
                                @foreach($countries as $country)
                                    <option value="{{ $country }}">{{ $country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Home Address --}}
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Home Address
                        </label>
                        <input type="text" name="alamatAsal" class="sima-input mt-1" placeholder="Full home address">
                    </div>

                    {{-- Indonesia Address --}}
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Indonesia Address
                        </label>
                        <input type="text" name="alamatIndo" class="sima-input mt-1" placeholder="Address in Indonesia">
                    </div>

                    {{-- Course Type + Online/Offline --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Course Type
                            </label>
                            <select name="tipeMahasiswa" class="sima-input mt-1">
                                <option value="">— Select Type —</option>
                                @foreach($tipeMhs as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Online / Offline
                            </label>
                            <div style="display:flex;gap:16px;margin-top:8px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--c-text-1);cursor:pointer;">
                                    <input type="radio" name="isOnline" value="1" style="accent-color:var(--c-accent);">
                                    Online
                                </label>
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--c-text-1);cursor:pointer;">
                                    <input type="radio" name="isOnline" value="0" checked style="accent-color:var(--c-accent);">
                                    Offline
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Jurusan + Status --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Jurusan <span style="color:var(--c-red)">*</span>
                            </label>
                            <select name="jurusan_id" id="jurusan_id" class="sima-input mt-1" required>
                                <option value="">Select Jurusan</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id }}">{{ $j->namaJurusan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                Status <span style="color:var(--c-red)">*</span>
                            </label>
                            <select name="status" class="sima-input mt-1" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending" selected>Pending</option>
                            </select>
                        </div>
                    </div>

                    {{-- NPM --}}
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                NPM
                            </label>
                            <div style="display:flex;gap:8px;margin-top:4px;">
                                <input type="text" name="npm" id="npmInput" class="sima-input" placeholder="Auto-generate if empty">
                                <button type="button" id="btnGenerateNpm" onclick="generateNpm()"
                                    class="sima-btn sima-btn--outline sima-btn--sm" style="white-space:nowrap;">
                                    <i class="fas fa-dice"></i> Generate
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6"></div>
                    </div>

                    {{-- Profile Photo --}}
                    <div>
                        <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                            Profile Photo
                        </label>
                        <div style="display:flex;align-items:center;gap:16px;margin-top:8px;">
                            <div id="photoPreviewCircle" style="width:72px;height:72px;border-radius:50%;background:var(--c-bg);border:2px dashed var(--c-border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                                <i class="fas fa-user" style="font-size:28px;color:var(--c-text-4);"></i>
                            </div>
                            <div>
                                <input type="file" name="fotoProfil" id="fotoProfil" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                                <button type="button" onclick="document.getElementById('fotoProfil').click()" class="sima-btn sima-btn--outline sima-btn--sm">
                                    <i class="fas fa-camera me-1"></i> Choose Photo
                                </button>
                                <div style="font-size:11px;color:var(--c-text-3);margin-top:4px;">JPG/PNG, max 2MB</div>
                            </div>
                        </div>
                    </div>

                    <div id="step1Err" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                    <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--c-border);">
                        <a href="{{ route('kln.users.page') }}" class="sima-btn sima-btn--outline">Cancel</a>
                        <button type="button" onclick="saveStep1()" class="sima-btn sima-btn--accent" id="btnStep1">
                            Save & Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     STEP 2 — EXTERNAL DOCUMENTS
     ═══════════════════════════════════════════════════ --}}
<div id="step2" style="display:none;">
    <div class="sima-card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
            <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Step 2 — External Documents</h5>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">Upload LOA, VISA, and Stay Permit documents</div>
        </div>
        <div style="padding:24px;">
            <form id="step2Form">
                <div style="display:flex;flex-direction:column;gap:20px;">

                    @php
                        $externalDocs = [
                            ['key' => 0, 'tipe' => 'LOA', 'label' => 'LOA (Letter of Acceptance)'],
                            ['key' => 1, 'tipe' => 'VISA', 'label' => 'VISA'],
                            ['key' => 2, 'tipe' => 'Stay_Permit', 'label' => 'Stay Permit (KITAS/KITAP)'],
                        ];
                    @endphp

                    @foreach($externalDocs as $doc)
                    <div style="background:var(--c-bg-2);border-radius:12px;padding:16px;border:1px solid var(--c-border);">
                        <div style="font-size:12px;font-weight:700;color:var(--c-text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em;">
                            <i class="fas fa-file-alt" style="margin-right:4px;"></i> {{ $doc['label'] }}
                        </div>
                        <input type="hidden" name="docs[{{ $doc['key'] }}][tipeDkmn]" value="{{ $doc['tipe'] }}">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Document Number
                                </label>
                                <input type="text" name="docs[{{ $doc['key'] }}][noDkmn]" class="sima-input mt-1" placeholder="Document number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Issuer
                                </label>
                                <select name="docs[{{ $doc['key'] }}][penerbit]" class="sima-input mt-1">
                                    <option value="">— Select Issuer —</option>
                                    @foreach($tipeDok as $td)
                                        <option value="{{ $td->penerbit ?? $td }}">{{ $td->penerbit ?? $td }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Issue Date
                                </label>
                                <input type="date" name="docs[{{ $doc['key'] }}][tglTerbit]" class="sima-input mt-1">
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Expiry Date
                                </label>
                                <input type="date" name="docs[{{ $doc['key'] }}][tglExpired]" class="sima-input mt-1">
                            </div>
                            <div class="col-12">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    File Upload
                                </label>
                                <input type="file" name="docs[{{ $doc['key'] }}][file]" class="sima-input mt-1" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div id="step2Err" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                    <div style="display:flex;gap:8px;justify-content:space-between;padding-top:12px;border-top:1px solid var(--c-border);">
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="goToStep(1)" class="sima-btn sima-btn--outline">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </button>
                            <button type="button" onclick="skipStep2()" class="sima-btn sima-btn--outline" style="color:var(--c-text-3);">
                                Skip this step
                            </button>
                        </div>
                        <button type="button" onclick="saveStep2()" class="sima-btn sima-btn--accent" id="btnStep2">
                            Save & Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     STEP 3 — INTERNAL DOCUMENTS
     ═══════════════════════════════════════════════════ --}}
<div id="step3" style="display:none;">
    <div class="sima-card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
            <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Step 3 — Internal Documents</h5>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">
                Typically managed by Jurusan. Upload if available.
            </div>
        </div>
        <div style="padding:24px;">
            <form id="step3Form">
                <div style="display:flex;flex-direction:column;gap:20px;">

                    @php
                        $internalDocs = [
                            ['key' => 0, 'tipe' => 'KRS', 'label' => 'KRS (Kartu Rencana Studi)'],
                            ['key' => 1, 'tipe' => 'Daftar_Nilai', 'label' => 'Daftar Nilai'],
                            ['key' => 2, 'tipe' => 'Jadwal', 'label' => 'Jadwal Kuliah'],
                            ['key' => 3, 'tipe' => 'Absensi', 'label' => 'Absensi'],
                        ];
                    @endphp

                    @foreach($internalDocs as $doc)
                    <div style="background:var(--c-bg-2);border-radius:12px;padding:16px;border:1px solid var(--c-border);">
                        <div style="font-size:12px;font-weight:700;color:var(--c-text-2);margin-bottom:14px;text-transform:uppercase;letter-spacing:.05em;">
                            <i class="fas fa-file-alt" style="margin-right:4px;"></i> {{ $doc['label'] }}
                        </div>
                        <div style="font-size:11.5px;color:var(--c-text-3);margin-top:-8px;margin-bottom:12px;font-style:italic;">
                            These are typically managed by Jurusan. Upload if available.
                        </div>
                        <input type="hidden" name="docs[{{ $doc['key'] }}][tipeDkmn]" value="{{ $doc['tipe'] }}">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Document Number
                                </label>
                                <input type="text" name="docs[{{ $doc['key'] }}][noDkmn]" class="sima-input mt-1" placeholder="Document number">
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Issuer
                                </label>
                                <select name="docs[{{ $doc['key'] }}][penerbit]" class="sima-input mt-1">
                                    <option value="">— Select Issuer —</option>
                                    @foreach($tipeDok as $td)
                                        <option value="{{ $td->penerbit ?? $td }}">{{ $td->penerbit ?? $td }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Issue Date
                                </label>
                                <input type="date" name="docs[{{ $doc['key'] }}][tglTerbit]" class="sima-input mt-1">
                            </div>
                            <div class="col-12 col-md-6">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    Expiry Date
                                </label>
                                <input type="date" name="docs[{{ $doc['key'] }}][tglExpired]" class="sima-input mt-1">
                            </div>
                            <div class="col-12">
                                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--c-text-2);">
                                    File Upload
                                </label>
                                <input type="file" name="docs[{{ $doc['key'] }}][file]" class="sima-input mt-1" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <div id="step3Err" style="display:none;font-size:13px;color:var(--c-red);padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid var(--c-red);border-radius:10px;"></div>

                    <div style="display:flex;gap:8px;justify-content:space-between;padding-top:12px;border-top:1px solid var(--c-border);">
                        <div style="display:flex;gap:8px;">
                            <button type="button" onclick="goToStep(2)" class="sima-btn sima-btn--outline">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </button>
                            <button type="button" onclick="skipStep3()" class="sima-btn sima-btn--outline" style="color:var(--c-text-3);">
                                Skip this step
                            </button>
                        </div>
                        <button type="button" onclick="saveStep3()" class="sima-btn sima-btn--accent" id="btnStep3">
                            Save & Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     STEP 4 — CHECKER
     ═══════════════════════════════════════════════════ --}}
<div id="step4" style="display:none;">
    <div class="sima-card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--c-border);">
            <h5 style="font-weight:700;font-family:var(--f-display);margin:0;">Step 4 — Completeness Checker</h5>
            <div style="font-size:13px;color:var(--c-text-3);margin-top:2px;">Review data completeness before finishing</div>
        </div>
        <div style="padding:24px;">

            <div id="checkerSummary" style="margin-bottom:20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <span id="checkerText" style="font-size:13px;font-weight:600;color:var(--c-text-2);"></span>
                    <span id="checkerPercent" style="font-size:12px;font-weight:600;color:var(--c-accent);"></span>
                </div>
                <div style="height:8px;background:var(--c-bg);border-radius:4px;overflow:hidden;">
                    <div id="checkerBar" style="height:100%;background:var(--c-accent);border-radius:4px;transition:width .4s ease;width:0%;"></div>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table class="sima-table" style="width:100%;">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Field</th>
                            <th style="width:100px;text-align:center;">Status</th>
                            <th style="width:100px;text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="checkerBody">
                        <tr>
                            <td colspan="4" style="text-align:center;padding:40px;color:var(--c-text-3);">
                                <i class="fas fa-spinner fa-spin" style="font-size:20px;"></i>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="display:flex;gap:8px;justify-content:space-between;padding-top:16px;border-top:1px solid var(--c-border);margin-top:20px;">
                <button type="button" onclick="goToStep(3)" class="sima-btn sima-btn--outline">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <button type="button" onclick="finishWizard()" class="sima-btn sima-btn--accent" id="btnFinish">
                    <i class="fas fa-check me-1"></i> Finish
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let currentStep = 1;

/* ── Step Navigation ──────────────────────────────── */
function goToStep(n) {
    for (let i = 1; i <= 4; i++) {
        document.getElementById('step' + i).style.display = i === n ? 'block' : 'none';
    }
    currentStep = n;
    updateStepIndicator();
}

function updateStepIndicator() {
    for (let i = 1; i <= 4; i++) {
        const circle = document.getElementById('stepCircle' + i);
        const line = document.getElementById('stepLine' + (i - 1));

        if (i < currentStep) {
            circle.style.background = '#059669';
            circle.style.color = '#fff';
            circle.style.border = '2px solid #059669';
            circle.innerHTML = '<i class="fas fa-check" style="font-size:13px;"></i>';
        } else if (i === currentStep) {
            circle.style.background = 'var(--c-accent)';
            circle.style.color = '#fff';
            circle.style.border = '2px solid var(--c-accent)';
            circle.style.boxShadow = '0 2px 8px rgba(108,143,255,.35)';
            circle.textContent = i;
        } else {
            circle.style.background = 'var(--c-surface)';
            circle.style.color = 'var(--c-text-3)';
            circle.style.border = '2px solid var(--c-border)';
            circle.style.boxShadow = 'none';
            circle.textContent = i;
        }

        if (line) {
            line.style.background = i < currentStep ? '#059669' : 'var(--c-border)';
        }
    }
}

/* ── Generate NPM ─────────────────────────────────── */
function generateNpm() {
    const jurusanId = document.getElementById('jurusan_id').value;
    if (!jurusanId) { alert('Please select a jurusan first'); return; }

    const btn = document.getElementById('btnGenerateNpm');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("kln.users.generate-npm") }}?jurusan_id=' + jurusanId)
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                document.getElementById('npmInput').value = res.npm;
            } else {
                alert(res.message || 'Failed to generate NPM');
            }
        })
        .catch(err => alert('Error: ' + err.message))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-dice"></i> Generate';
        });
}

/* ── Photo Preview ────────────────────────────────── */
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPreviewCircle').innerHTML =
                '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

/* ── Step 1 Save ──────────────────────────────────── */
function saveStep1() {
    const errDiv = document.getElementById('step1Err');
    errDiv.style.display = 'none';

    const form = document.getElementById('step1Form');
    const formData = new FormData(form);

    const nama = formData.get('nama');
    const email = formData.get('email');
    const jurusanId = formData.get('jurusan_id');

    if (!nama) { alert('Name is required'); return; }
    if (!email || !email.includes('@')) { alert('Valid email is required'); return; }
    if (!jurusanId) { alert('Jurusan is required'); return; }

    const btn = document.getElementById('btnStep1');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("kln.users.mahasiswa.step1") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async res => {
        const text = await res.text();
        try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
    })
    .then(response => {
        if (response.success) {
            document.getElementById('currentUserId').value = response.user_id;
            goToStep(2);
        } else {
            let msg = response.message || 'Validation failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Save & Next <i class="fas fa-arrow-right ms-1"></i>';
    });
}

/* ── Step 2 Save ──────────────────────────────────── */
function saveStep2() {
    const errDiv = document.getElementById('step2Err');
    errDiv.style.display = 'none';

    const form = document.getElementById('step2Form');
    const formData = new FormData(form);
    formData.append('user_id', document.getElementById('currentUserId').value);

    const btn = document.getElementById('btnStep2');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("kln.users.mahasiswa.step2") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async res => {
        const text = await res.text();
        try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
    })
    .then(response => {
        if (response.success) {
            goToStep(3);
        } else {
            let msg = response.message || 'Save failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Save & Next <i class="fas fa-arrow-right ms-1"></i>';
    });
}

function skipStep2() {
    goToStep(3);
}

/* ── Step 3 Save ──────────────────────────────────── */
function saveStep3() {
    const errDiv = document.getElementById('step3Err');
    errDiv.style.display = 'none';

    const form = document.getElementById('step3Form');
    const formData = new FormData(form);
    formData.append('user_id', document.getElementById('currentUserId').value);

    const btn = document.getElementById('btnStep3');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';

    fetch('{{ route("kln.users.mahasiswa.step3") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(async res => {
        const text = await res.text();
        try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
    })
    .then(response => {
        if (response.success) {
            goToStep(4);
            loadChecker();
        } else {
            let msg = response.message || 'Save failed';
            if (response.errors) {
                msg += ': ' + Object.values(response.errors).flat().join(', ');
            }
            errDiv.textContent = msg;
            errDiv.style.display = 'block';
        }
    })
    .catch(error => {
        errDiv.textContent = 'Server error: ' + error.message;
        errDiv.style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Save & Next <i class="fas fa-arrow-right ms-1"></i>';
    });
}

function skipStep3() {
    goToStep(4);
    loadChecker();
}

/* ── Load Checker ─────────────────────────────────── */
function loadChecker() {
    const userId = document.getElementById('currentUserId').value;
    const body = document.getElementById('checkerBody');
    body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--c-text-3);"><i class="fas fa-spinner fa-spin" style="font-size:20px;"></i></td></tr>';

    fetch('{{ route("kln.users.mahasiswa.checker", ":userId") }}'.replace(':userId', userId))
        .then(async res => {
            const text = await res.text();
            try { return JSON.parse(text); } catch { throw new Error('HTTP ' + res.status); }
        })
        .then(data => {
            if (!data.success) {
                body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--c-red);">Failed to load checker</td></tr>';
                return;
            }

            const items = data.items || [];
            const total = items.length;
            const done = items.filter(i => i.status).length;
            const pct = total > 0 ? Math.round((done / total) * 100) : 0;

            document.getElementById('checkerText').textContent = done + ' of ' + total + ' required items completed';
            document.getElementById('checkerPercent').textContent = pct + '%';
            document.getElementById('checkerBar').style.width = pct + '%';
            document.getElementById('checkerBar').style.background = pct === 100 ? '#059669' : 'var(--c-accent)';

            if (items.length === 0) {
                body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--c-text-3);">No items to check</td></tr>';
                return;
            }

            body.innerHTML = items.map((item, idx) => {
                const icon = item.status
                    ? '<span style="color:#059669;font-size:16px;">&#x2705;</span>'
                    : '<span style="color:var(--c-red);font-size:16px;">&#x274C;</span>';
                const action = item.step
                    ? '<button onclick="goToStep(' + item.step + ')" class="sima-btn sima-btn--outline sima-btn--sm" style="font-size:11px;padding:4px 10px;"><i class="fas fa-pen"></i></button>'
                    : '';
                return '<tr>' +
                    '<td style="font-size:12px;color:var(--c-text-3);">' + (idx + 1) + '</td>' +
                    '<td style="font-weight:600;color:var(--c-text-1);font-size:13px;">' + item.name + '</td>' +
                    '<td style="text-align:center;">' + icon + '</td>' +
                    '<td style="text-align:center;">' + action + '</td>' +
                    '</tr>';
            }).join('');
        })
        .catch(error => {
            body.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:40px;color:var(--c-red);">Error: ' + error.message + '</td></tr>';
        });
}

/* ── Finish ───────────────────────────────────────── */
function finishWizard() {
    window.location.href = '{{ route("kln.users.page") }}?created=1';
}
</script>
@endpush
