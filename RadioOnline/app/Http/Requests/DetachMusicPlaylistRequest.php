<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetachMusicPlaylistRequest extends FormRequest
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
            'playlist_id' => 'required|integer|exists:playlists,id,playlist_type,music',
            'musics' => 'required|array',
            'musics.*.music_id' => 'required|integer|exists:musics,id',
        ];
    }

}
