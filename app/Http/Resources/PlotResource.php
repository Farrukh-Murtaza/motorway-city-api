<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'block' => $this->block,
            'streetNo' => $this->street_no,

            'width' => (double)$this->width,
            'length' => (double)$this->length,
            
            'isCorner' => (bool) $this->is_corner,
            'isParkFace' => (bool) $this->is_park_face,
            'isFortyFeet' => (bool) $this->is_forty_feet,
            
            'status' => $this->status,
            'size' => $this->size,
            'marla' => $this->marla,
            'category' => $this->category,

            // 'plotSales' => $this->whenLoaded('activePlotSale', function () {
            //     return new PlotSaleResource($this->activePlotSale);
            // }),
        
            // Computed attributes
            // 'is_available' => $this->isAvailable(),
           
            // 'is_sold' => $this->isSold(),
            // 'full_address' => $this->full_address,
            
        ];
    }
}
