<?php

namespace App\Http\Controllers;

use App\Services\DocumentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AcademicDocumentController extends Controller
{
    public const TYPES = ['KRS' => 'KRS', 'FRS' => 'FRS', 'Daftar_Nilai' => 'Daftar Nilai', 'Absensi' => 'Kehadiran'];

    private function students(Request $request)
    {
        $jurusanId = $request->user()->jurusan_id;
        abort_unless($jurusanId, 403, 'Akun belum terhubung dengan jurusan.');

        return DB::table('mahasiswa')->join('users', 'mahasiswa.user_id', '=', 'users.id')
            ->where('users.jurusan_id', $jurusanId);
    }

    private function document(Request $request, int $id)
    {
        $document = DB::table('dokumen')->where('id', $id)->whereNull('deleted_at')
            ->where('penerbit', 'UNIVERSITAS')->whereIn('tipeDkmn', array_keys(self::TYPES))->first();
        abort_unless($document && $this->students($request)->where('mahasiswa.id', $document->mahasiswa_id)->exists(), 404);

        return $document;
    }

    public function create(Request $request)
    {
        $students = $this->students($request)->select('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm')->orderBy('mahasiswa.nama')->get();
        return view('jurusan.dokumen.form', ['students' => $students, 'types' => self::TYPES, 'document' => null]);
    }

    public function edit(Request $request, int $id)
    {
        $document = $this->document($request, $id);
        $students = $this->students($request)->where('mahasiswa.id', $document->mahasiswa_id)
            ->select('mahasiswa.id', 'mahasiswa.nama', 'mahasiswa.npm')->get();
        return view('jurusan.dokumen.form', ['students' => $students, 'types' => self::TYPES, 'document' => $document]);
    }

    public function store(Request $request, DocumentNotification $notifications)
    {
        return $this->save($request, $notifications);
    }

    public function update(Request $request, int $id, DocumentNotification $notifications)
    {
        return $this->save($request, $notifications, $this->document($request, $id));
    }

    private function save(Request $request, DocumentNotification $notifications, ?object $document = null)
    {
        $data = $request->validate([
            'mahasiswa_id' => ['required', 'integer'],
            'tipeDkmn' => ['required', Rule::in(array_keys(self::TYPES))],
            'namaDkmn' => ['required', 'string', 'max:150'],
            'noDkmn' => ['nullable', 'string', 'max:100'],
            'tglTerbit' => ['required', 'date'],
            'tglKdlwrs' => ['required', 'date', 'after_or_equal:tglTerbit'],
            'file' => [$document ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png,xls,xlsx,csv', 'max:10240'],
        ]);
        $student = $this->students($request)->where('mahasiswa.id', $data['mahasiswa_id'])->select('mahasiswa.*')->first();
        abort_unless($student, 403, 'Mahasiswa tidak berada di jurusan Anda.');
        abort_if($document && (int) $document->mahasiswa_id !== (int) $student->id, 422, 'Pemilik dokumen tidak dapat diubah.');

        $file = $request->file('file');
        $path = $file ? $file->store('dokumen/jurusan/'.$student->id, 'local') : null;
        abort_if($file && !$path, 500, 'Penyimpanan file gagal. Silakan coba kembali.');
        unset($data['file']);
        $data['noDkmn'] = ($data['noDkmn'] ?? null) ?: ($document->noDkmn ?? 'AKD-'.Str::uuid());
        $data['penerbit'] = 'UNIVERSITAS';
        $data['status'] = 'approved';
        $data['updated_at'] = now();

        try {
            DB::transaction(function () use ($data, $document, $file, $path, $student, $notifications) {
                if ($document) {
                    // Lock the row so a concurrent delete cannot resurrect a document.
                    $current = DB::table('dokumen')->where('id', $document->id)->whereNull('deleted_at')->lockForUpdate()->first();
                    abort_unless($current, 404);
                    DB::table('dokumen')->where('id', $document->id)->update($data);
                    $id = $document->id;
                } else {
                    $id = DB::table('dokumen')->insertGetId($data + ['created_at' => now()]);
                }
                if ($file) {
                    DB::table('fileDetail')->insert([
                        'dokumen_id' => $id, 'path' => $path, 'mimeType' => $file->getMimeType(), 'fileSize' => $file->getSize(),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
                DB::table('historyDokumen')->insert([
                    'mahasiswa_id' => $student->id, 'dokumen_id' => $id, 'user_id' => auth()->id(),
                    'message' => 'Dokumen akademik '.($document ? 'diperbarui' : 'diunggah').' oleh Jurusan.',
                    'action' => $document ? 'UPDATE' : 'UPLOAD', 'status_from' => $document->status ?? null,
                    'status_to' => 'approved', 'created_at' => now(), 'updated_at' => now(),
                ]);
                $notifications->student($student->id, 'Dokumen Akademik dari Jurusan',
                    $data['namaDkmn'].' tersedia. Unduh melalui menu Documents & Requests.');
            });
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('local')->delete($path);
            }
            throw $e;
        }

        return redirect()->route('jurusan.dokumen.index')->with('success', 'Dokumen akademik berhasil disimpan dan tersedia untuk mahasiswa.');
    }

    public function destroy(Request $request, int $id)
    {
        $this->document($request, $id);
        DB::table('dokumen')->where('id', $id)->update(['deleted_at' => now(), 'updated_at' => now()]);
        return redirect()->route('jurusan.dokumen.index')->with('success', 'Dokumen akademik berhasil diarsipkan.');
    }
}
