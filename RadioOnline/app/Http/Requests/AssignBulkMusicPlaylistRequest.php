<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignBulkMusicPlaylistRequest extends FormRequest
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
            'assign' => 'required|array',
            'assign.*.music_id' => 'required|integer|exists:musics,id',
            'assign.*.playlist_id' => 'required|integer|exists:playlists,id,playlist_type,music',
        ];
    }
}
