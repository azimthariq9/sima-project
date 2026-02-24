<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\kelas;
class updateKelasRequest extends FormRequest
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
            'kodeKelas' => ['sometimes', 'required', 'string', 'max:255', 'unique:kelas,kodeKelas', Rule::unique(kelas::class)->ignore($this->route('kelas')->id)],
        ];
    }
}
