<?php

namespace App\Http\Requests;

use App\Traits\PlaylistTrait;
use Illuminate\Foundation\Http\FormRequest;

class AssignMusicPlaylistRequest extends FormRequest
{
    use PlaylistTrait;

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
            'playlist_id' => [
                'required',
                'integer',
                'exists:playlists,id,playlist_type,music',
                function ($attribute, $value, $fail) {
                    if ($this->isPlaylistPlaying($value, $name)) {
                        $fail(__('playlist.validation.playlist_is_playing', ['playlist_name' => $name]));
                    }
                }],
            'musics' => 'required|array',
            'musics.*.music_id' => 'required|integer|exists:musics,id',
        ];
    }

}
