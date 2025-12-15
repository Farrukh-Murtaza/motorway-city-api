<?php

namespace App\Http\Resources;

use App\Models\PlotSale;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
       $data = parent::toArray($request);

      return [

            // Nested person info (camelCase)
           
                'id'                 => $this->person->id,
                'firstName'          => $this->person->first_name,
                'lastName'           => $this->person->last_name,
                'name'           => $this->person->name,
                'fatherOrHusbandName'=> $this->person->father_or_husband_name,
                'gender'             => $this->person->gender,
                'mobile'             => $this->person->mobile,
                'phone'              => $this->person->phone,
                'whatsapp'           => $this->person->whatsapp,
                'email'              => $this->person->email,
                'cnic'               => $this->person->cnic,
                'dob'                => $this->person->dob,
                'postalAddress'      => $this->person->postal_address,
                'residentialAddress' => $this->person->residential_address,
                'personImg'          => $this->person->person_img,
           
        ];

        return $data;
    }
}
