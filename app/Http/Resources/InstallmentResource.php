<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstallmentResource extends JsonResource
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
            'plotSaleId' => $this->plot_sale_id,
            'plotId' => $this->plot_id,
            'installmentNumber' => $this->installment_number,
            'dueDate' => $this->due_date ? $this->due_date->format('Y-m-d') : null,
            'originalAmount' => (float) $this->original_amount,
            'paidAmount' => (float) $this->paid_amount,
            'remainingAmount' => (float) $this->original_amount - $this->paid_amount,
            'status' => $this->status,
            'paymentDate' => $this->payment_date ,
            
        ];
    }
}
