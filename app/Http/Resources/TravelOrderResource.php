<?php

namespace App\Http\Resources;

use App\Enum\TravelOrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'applicant' => $this->applicant_name,
            'destination' => $this->destination,
            'departure_date' => $this->departure_date->format('Y-m-d H:i:s'),
            'return_date' => $this->return_date->format('Y-m-d H:i:s'),
            'status' => TravelOrderStatus::toName($this->status),
        ];
    }
}
