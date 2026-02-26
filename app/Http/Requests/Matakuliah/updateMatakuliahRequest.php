<?php

namespace App\Http\Requests\Matakuliah;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\matakuliah;

class updateMatakuliahRequest extends FormRequest
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
            'namaMk' => ['sometimes','required', 'string', 'max:255'],
            'kodeMk' => ['sometimes','required', 'string', 'max:255', Rule::unique('matakuliah', 'kodemk')->ignore($this->matakuliah->id)],
            'sks' => ['sometimes','required', 'integer', 'min:1'],
            'keterangan' => ['sometimes','nullable', 'string', 'max:5'],
        ];
    }
}
