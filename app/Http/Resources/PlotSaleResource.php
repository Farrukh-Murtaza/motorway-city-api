<?php

namespace App\Http\Resources;

use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlotSaleResource extends JsonResource
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
            'plotId' => $this->plot_id,
            'personId' => $this->person_id,
            'nomineeId' => $this->nominee_id,
            'nomineeRelationId' => $this->nominee_relation_id,
            'bookingDate' => $this->booking_date,
            'registrationNo' => $this->registration_no,
            'applicationFormNo' => $this->application_form_no,
            'installmentAmount' =>  (double)  $this->installment_amount,
            'totalInstallments' => $this->total_installments,
            'installmentPeriod' =>  $this->installment_period,
            'bookingStatus' => $this->booking_status,
            'paidInstallments' => $this->paid_installments,
            'nextInstallmentDate' => $this->next_installment_date,
            'paymentType' => $this->payment_type,
            'paymentMethod' => $this->payment_method,
            'marlaRate' =>   $this->marla_rate,
            'downPayment' =>   $this->down_payment,
            'totalAmount' =>   $this->total_amount,
            'amountPaid' =>   $this->amount_paid,
            'remainingAmount' =>   $this->remainingAmount,
            'cornerCharges' =>   $this->corner_charges,
            'parkFacingCharges' =>   $this->park_facing_charges,
            'completionDate' => $this->completion_date,
            'cancellationDate' => $this->cancellation_date,
            'cancellationReason' => $this->cancellation_reason,
            'notes' => $this->notes,
            'installments' => InstallmentResource::collection($this->installments),
            
            // Fixed relationships - use whenLoaded and new Resource for single objects
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                ];
            }),
           
                
        ];  
    }
}