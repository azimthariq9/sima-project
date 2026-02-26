<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\status;

class createAnnouncementRequest extends FormRequest
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
            // 'user_id' => ['required', 'exists:users,id'],
            // 'subject' => ['required', 'string', 'max:255'],
            // 'message' => ['required', 'string'],
            // 'status' => ['required', new Enum(status::class)],
            // 'mahasiswa_ids' => ['required', 'array'],
            // 'mahasiswa_ids.*' => ['exists:mahasiswa,id'],

        ];
    }
}
