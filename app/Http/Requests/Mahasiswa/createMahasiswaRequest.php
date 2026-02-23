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
            'nama' => 'required|string|max:255',
            'npm' => 'required|string|max:20|unique:mahasiswa,npm',
            'noWa' => 'required|string|max:20|unique:mahasiswa,noWa',
            'tglLahir' => 'required|date',
            'warNeg' => 'required|string|max:50',
            'alamatAsal' => 'required|string|max:255',
            'alamatIndo' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'jurusan_id' => 'required|exists:jurusan,id',
            
            
        ];
    }
}
