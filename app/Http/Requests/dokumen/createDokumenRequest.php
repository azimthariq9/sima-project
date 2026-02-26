<?php

namespace App\Http\Requests\Dokumen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\tipeDok;
use App\Enums\status;
use App\Enums\penerbit;

class createDokumenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mahasiswa_id' => ['sometimes', 'exists:mahasiswa,id'],
            'tipeDkmn' => ['sometimes','required', new Enum(tipeDok::class)],
            'namaDkmn' => ['sometimes','required', 'string', 'max:255'],
            'penerbit' => ['sometimes','required', new Enum(penerbit::class)],
            'noDkmn' => ['sometimes','required', 'string', 'max:255'],
            'tglTerbit' => ['sometimes','required', 'date'],
            'tglKdlwrs' => ['sometimes','required', 'date', 'after_or_equal:tglTerbit'],
            'status' => ['sometimes','required', new Enum(status::class)],

        ];
    }
}
