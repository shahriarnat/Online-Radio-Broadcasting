<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlaylistRequest extends FormRequest
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
            'name' => 'required|string|unique:playlists,name|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'required|file|mimes:jpeg,png,jpg|max:512',
            'start_play' => 'required|date_format:H:i',
            'end_play' => 'required|date_format:H:i|after_or_equal:start_play',
            'activate' => 'required|boolean',
        ];
    }
}
