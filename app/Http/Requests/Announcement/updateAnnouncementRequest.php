<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\status;
class updateAnnouncementRequest extends FormRequest
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
            'users_id' => 'sometimes|required|exists:users,id',
            'subject' => 'sometimes|required|string|max:255',
            'message' => 'sometimes|required|string',
            'status' => ['sometimes', new Enum(status::class)],
            'mahasiswa_ids' => 'sometimes|required|array',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
        ];
    }
}
