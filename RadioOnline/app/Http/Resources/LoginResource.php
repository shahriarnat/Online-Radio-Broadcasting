<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access' => [
                'token_type' => 'Bearer',
                'token' => $this->auth->plainTextToken,
                'token_life' => $this->auth->accessToken->expires_at,
                'token_life_timestamp' => Carbon::create($this->auth->accessToken->expires_at)->timestamp,
            ],
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
            ],
        ];
    }
}
