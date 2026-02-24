<?php

namespace App\Http\Requests\HistoryDok;

use Illuminate\Foundation\Http\FormRequest;

class createHistoryDokRequest extends FormRequest
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
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'dokumen_id' => ['required', 'exists:dokumen,id'],
            'users_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:255'],
        ];
    }
}
