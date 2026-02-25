<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Auth;
use App\Enums\Status;
use App\Enums\Role;
use App\Models\User;
class updateUserRequest extends FormRequest
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
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user')->id)],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', new Enum(Role::class)],
            'status' => ['sometimes', new Enum(Status::class)],
            'jurusan_id' => ['sometimes', 'exists:jurusan,id'],
        ];
    }
}
