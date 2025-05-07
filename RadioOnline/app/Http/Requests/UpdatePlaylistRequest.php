<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlaylistRequest extends FormRequest
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
            'id' => 'required|integer|exists:playlists,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('playlists', 'name')->whereNotIn('id', [$this->id]),
            ],
            'description' => 'nullable|string|max:1000',
            'start_play' => 'required|date_format:H:i',
            'end_play' => 'required|date_format:H:i|after_or_equal:start_play',
            'activate' => 'required|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

}
