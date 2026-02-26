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
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'tipeDkmn' => ['required', new Enum(tipeDok::class)],
            'namaDkmn' => ['required', 'string', 'max:255'],
            'penerbit' => ['required', new Enum(penerbit::class)],
            'noDkmn' => ['required', 'string', 'max:255'],
            'tglTerbit' => ['required', 'date'],
            'tglkdlwrs' => ['required', 'date', 'after_or_equal:tglTerbit'],
            'status' => ['required', new Enum(status::class)],

        ];
    }
}
