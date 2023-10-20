<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProfileUserGeneralResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'surname' => $this->resource->surname,
            'birthdate' => $this->resource->birthdate ? Carbon::parse($this->resource->birthdate)->format('Y-m-d') : null,
            'email' => $this->resource->email,
            'phone' => $this->resource->phone,
            'address' => $this->resource->address,
            'website' => $this->resource->website,
            'avatar' => $this->resource->avatar ? env('APP_URL') . 'storage/' .  $this->resource->avatar : null,
            // 'avatar' => $this->resource->avatar ? env('APP_URL') . 'storage/' .  $this->resource->avatar : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
            'fb' => $this->resource->fb,
            'tw' => $this->resource->tw,
            'ig' => $this->resource->ig,
            'lnkdn' => $this->resource->lnkdn,
        ];
    }
}
