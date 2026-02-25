<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;
use App\Enums\Status;
use App\Enums\Role;

class createUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === Role::KLN || auth()->user()->role === Role::adminJurusan);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', new Enum(Role::class)],
            'status' => ['required', new Enum(Status::class)],
            'jurusan_id' => ['required', 'exists:jurusan,id'],

            //mahasiswa
            'mahasiswa' => ['required_if:role,mahasiswa', 'array'],
            'mahasiswa.npm' => ['required_if:role,mahasiswa', 'string', 'max:20', 'unique:mahasiswa,npm'],
            'mahasiswa.nama' => ['required_if:role,mahasiswa', 'string', 'max:255'],

            //dosen
            'dosen' => ['required_if:role,dosen', 'array'],
            'dosen.nama' => ['required_if:role,dosen', 'string', 'max:255'],
            'dosen.nidn' => ['required_if:role,dosen', 'string', 'max:255', 'unique:dosen,nidn'],
            'dosen.kodeDos' => ['required_if:role,dosen', 'string', 'max:255', 'unique:dosen,kodeDos'],
        ];
    }
}
