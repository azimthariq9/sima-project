<?php
// app/Http/Requests/Dokumen/UploadDokumenRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\tipeDok;
use App\Enums\penerbit;

class UploadDokumenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf', 'max:2048'], // 2MB
            'tipeDkmn' => ['required', new Enum(tipeDok::class)],
            'namaDkmn' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', new Enum(penerbit::class)],
            'noDkmn' => ['required', 'string', 'max:255'],
            'tglTerbit' => ['required', 'date'],
            'tglkdlwrs' => ['required', 'date', 'after_or_equal:tglTerbit'],
            'reqDokumen_id' => ['sometimes', 'exists:reqDokumen,id'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'file.required' => 'File dokumen wajib diupload',
            'file.mimes' => 'File harus berformat PDF',
            'file.max' => 'Ukuran file maksimal 2MB',
            'tglkdlwrs.after_or_equal' => 'Tanggal kadaluarsa harus setelah atau sama dengan tanggal terbit',
        ];
    }
}
