<?php

namespace App\Http\Requests\log;

use Illuminate\Foundation\Http\FormRequest;

class createLogRequest extends FormRequest
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
            'user_id' => ['required', 'exists:users,id'],
            'mahasiswa_id' => ['nullable', 'exists:mahasiswa,id'],
            'jadwal_id' => ['nullable', 'exists:jadwal,id'],
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'kelas_id' => ['nullable','exists:kelas,id'],
            'matakuliah_id' => ['nullable', 'exists:mata_kuliah,id'],
            'jurusan_id' => ['nullable','exists:jurusan,id'],
            'notification_id' => ['nullable', 'exists:notification,id'],
            'announcement_id' => ['nullable', 'exists:announcement,id'],
            'aksi' => ['required', 'string', 'max:255'],
        ];
    }
}
