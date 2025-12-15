<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'people' => PeopleResource::collection($this->resource['people'] ?? []),
            'plots' => PlotResource::collection($this->resource['plots'] ?? []),
            'plotSales' => PlotSaleResource::collection($this->resource['plotSales'] ?? [])
        ];
    }
}
