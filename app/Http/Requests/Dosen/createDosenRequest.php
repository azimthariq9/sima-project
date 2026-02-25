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
            'nama' => ['required', 'string', 'max:255'],
            'nidn' => ['required', 'string', 'max:255', 'unique:dosen,nidn'],
            'kodeDos'=> ['required', 'string', 'max:255', 'unique:dosen,kodeDos'],
            'users_id' => ['required', 'exists:users,id'],
        ];
    }
}
