<?php

namespace App\Http\Requests\Dosen;

use Illuminate\Foundation\Http\FormRequest;

class createDosenRequest extends FormRequest
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
            'nama' => ['sometimes', 'required', 'string', 'max:255'],
            'nidn' => ['sometimes', 'required', 'string', 'max:255', 'unique:dosen,nidn'],
            'kodeDos'=> ['sometimes', 'required', 'string', 'max:255', 'unique:dosen,kodeDos'],
            'users_id' => ['sometimes', 'required', 'exists:users,id'],
        ];
    }
}
