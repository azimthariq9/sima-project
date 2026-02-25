<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class formMahasiswa extends FormRequest
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
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'npm' => ['sometimes', 'required', 'string', 'max:20', 'unique:mahasiswa,npm'],
            'noWa' => ['sometimes', 'required', 'string', 'max:20', 'unique:mahasiswa,noWa'],
            'tglLahir' => ['sometimes', 'required', 'date'],
            'warNeg' => ['sometimes', 'required', 'string', 'max:50'],
            'alamatAsal' => ['sometimes', 'required', 'string', 'max:255'],
            'alamatIndo' => ['sometimes', 'required', 'string', 'max:255'],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            
            
        ];
    }
}
