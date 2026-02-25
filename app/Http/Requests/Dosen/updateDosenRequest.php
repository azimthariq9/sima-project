<?php

namespace App\Http\Requests\Dosen;

use App\Models\dosen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class updateDosenRequest extends FormRequest
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
            'nidn' => ['sometimes', 'required', 'string', 'max:255', 'unique:dosen,nidn', Rule::unique(Dosen::class)->ignore($this->route('dosen')->id)],
            'kodeDos'=> ['sometimes', 'required', 'string', 'max:255', 'unique:dosen,kodeDos', Rule::unique(Dosen::class)->ignore($this->route('dosen')->id)],
            'users_id' => ['sometimes', 'required', 'exists:users,id'],
        ];
    }
}
