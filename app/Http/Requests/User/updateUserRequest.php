<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;
use App\Enums\Status;
use App\Enums\Role;
use App\Models\dosen;
use App\Models\Mahasiswa;
use App\Models\User;
class updateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->role === Role::KLN || auth()->user()->role === Role::JURUSAN);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user'); // Ini akan terisi jika pakai route model binding
        return [
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8', 'nullable'],
            'role' => ['sometimes', new Enum(Role::class)],
            'status' => ['sometimes', new Enum(Status::class)],
            'jurusan_id' => ['sometimes', 'exists:jurusan,id'],
            //mahasiswa
            'mahasiswa' => ['required_if:role,mahasiswa', 'array'],
            'mahasiswa.npm' => ['sometimes', 'string', 'max:20', Rule::unique(Mahasiswa::class)->ignore($user->mahasiswa?->id)],
            'mahasiswa.nama' => ['sometimes', 'string', 'max:255'],

            //dosen
            'dosen' => ['required_if:role,dosen', 'array'],
            'dosen.nama' => ['sometimes', 'string', 'max:255'],
            'dosen.nidn' => ['sometimes', 'string', 'max:255', Rule::unique(dosen::class)->ignore($user->dosen?->id)],
            'dosen.kodeDos' => ['sometimes','string', 'max:255', Rule::unique(dosen::class)->ignore($user->dosen?->id)],
        ];
    }
}
