<?php

namespace App\Http\Requests\Jadwal;

use Illuminate\Foundation\Http\FormRequest;

class createJadwalRequest extends FormRequest
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
            'kelas_id' => ['required', 'exists:kelas,id'],
            'matakuliah_id' => ['required', 'exists:matakuliah,id'],
            'dosen_id' => ['required', 'exists:dosen,id'],
            'hari' => ['required', 'string', 'max:10'],
            'jam' => ['required', 'string', 'max:20'],
            'ruangan' => ['required', 'string', 'max:50'],
            'totalSesi' => ['required', 'integer', 'min:1'],

        ];
    }
}
