<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowMusicsRequest extends FormRequest
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
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|string|in:title,artist,duration,guest_like',
            'sort_order' => 'nullable|string|in:asc,desc',

            'title' => 'nullable|string',
            'artist' => 'nullable|string',
            'is_ads' => 'nullable|boolean',

            'genre_id' => 'nullable|integer|exists:genres,id',
            'playlist_id' => 'nullable|integer|exists:playlists,id',
            'channel_id' => 'nullable|integer|exists:channels,id',

        ];
    }
}
