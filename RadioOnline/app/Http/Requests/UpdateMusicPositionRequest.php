<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMusicPositionRequest extends FormRequest
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
            'musics' => 'required|array',
            'musics.*.music_id' => 'required|integer|exists:playlist_music,music_id',
            'musics.*.position' => 'required|integer|min:1',
            'playlist_id' => 'required|integer|exists:playlist_music,playlist_id',
        ];
    }
}
