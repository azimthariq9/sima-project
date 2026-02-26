<?php

namespace App\Http\Requests\ReqDokumen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\tipeDok;
use App\Enums\status;
use App\Enums\penerbit;
class createReqDokumenRequest extends FormRequest
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
            'mahasiswa_id' => ['sometimes','required', 'exists:mahasiswa,id'],
            'tipeDkmn' => ['sometimes','required', new Enum(tipeDok::class)],
            'namaDkmn' => ['sometimes','required', 'string', 'max:255'],
            'message'=>['required','string','max:255'],
            'status' => ['sometimes','required', new Enum(status::class)],
            'user_id' => ['sometimes','required', 'exists:users,id']
        ];
    }
}
