<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeopleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'firstName'          => $this->first_name,
            'lastName'           => $this->last_name,
            'name'           => $this->name,
            'fatherOrHusbandName'=> $this->father_or_husband_name,
            'gender'             => $this->gender,
             'occupation' => $this->occupation ? [
                'id' => $this->occupation->id,
                'name' => $this->occupation->name,
            ] : null,
            'cnic'               => $this->cnic,
            'mobile'             => $this->mobile,
            'phone'              => $this->phone,
            'whatsapp'           => $this->whatsapp,
            'email'              => $this->email,
            'dob'                => $this->dob,
            'postalAddress'      => $this->postal_address,
            'residentialAddress' => $this->residential_address,
            'personImg'          => $this->person_img,
            'cnicImg'          => $this->cnic_img,
        ];
    }
}
