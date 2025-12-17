<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name','last_name','father_or_husband_name','gender','mobile','phone','whatsapp','occupation_id',
        'email','cnic','dob','postal_address','residential_address','person_img', 'cnic_img'
    ];

    public function customer() {
        return $this->hasOne(Customer::class);
    }

     public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

     public function getNameAttribute()
    {
        return ucfirst($this->first_name) ." ".ucfirst($this->last_name);
    }
    
}
