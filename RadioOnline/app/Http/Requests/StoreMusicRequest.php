<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMusicRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'artist' => 'required|string|max:255',
            'album' => 'nullable|string|max:255',
            'cover' => 'required|file|mimes:jpg,jpeg,png|max:512|image,dimensions:min_width=200,min_height=200',
            'file' => 'required|file|mimes:mp3',
            'duration' => 'required|integer|min:1',
            /* @todo add genre reference */
            'genre' => 'nullable|string|max:255',
            'is_ads' => 'required|boolean',
        ];

    }
}
