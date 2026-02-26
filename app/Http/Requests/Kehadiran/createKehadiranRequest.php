<?php

namespace App\Http\Requests\Kehadiran;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\status;

class createKehadiranRequest extends FormRequest
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
            'jadwal_id' => ['required', 'exists:jadwal,id'],
            'sesi' => ['required', 'integer', 'min:1'],
            'status' => ['required', new Enum(status::class)],
            'tglSesi' => ['required', 'date', 'nullable'],
        ];
    }
}
