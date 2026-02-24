<?php

namespace App\Http\Requests\Dokumen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\status;
use App\Enums\penerbit;

class updateDokumenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'namaDkmn' => ['sometimes', 'required', 'string', 'max:255'],
            'penerbit' => ['sometimes', new Enum(penerbit::class)],
            'noDkmn' => ['sometimes', 'required', 'string', 'max:255'],
            'tglTerbit' => ['sometimes', 'required', 'date'],
            'tglkdlwrs' => ['sometimes', 'required', 'date', 'after_or_equal:tglTerbit'],
            'status' => ['sometimes', new Enum(status::class)],
            'path' => ['sometimes', 'required', 'string', 'max:255'],
            'mimeType' => ['sometimes', 'required', 'string', 'max:50'],
            'size' => ['sometimes', 'required', 'integer'],
        ];
    }
}
