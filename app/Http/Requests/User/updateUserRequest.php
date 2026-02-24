<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use App\Enums\status;
use App\Enums\role;
use App\Models\User;
class updateUserRequest extends FormRequest
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
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($this->route('user')->id)],
            'password' => ['sometimes', 'string', 'min:8'],
            'role' => ['sometimes', new Enum(role::class)],
            'status' => ['sometimes', new Enum(status::class)],
        ];
    }
}
