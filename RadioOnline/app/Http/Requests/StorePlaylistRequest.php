<?php

namespace App\Http\Requests;

use App\Rules\PlaylistOverlapPreventRule;
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
            'channel_playlist' => 'required|exists:channels,id',
            'name' => ['required', 'string', 'max:255', new PlaylistOverlapPreventRule(
                $this->input('channel_playlist'),
                $this->input('start_date'),
                $this->input('end_date'),
                $this->input('start_time'),
                $this->input('end_time')
            )],
            'description' => 'nullable|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'activate' => 'required|boolean',
        ];
    }
}
