<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'plot_sale_id' => $this->plot_sale_id,
            'payment_reference' => $this->payment_reference,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'payment_date' => $this->payment_date,
            'payment_type' => $this->payment_type,
            'installment_number' => $this->installment_number,
            'notes' => $this->notes,
            'attachment' => $this->attachment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'plot' => new PlotResource($this->plot),
        ];
    }
}
