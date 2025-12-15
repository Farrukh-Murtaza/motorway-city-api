<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['person_id','occupation_id','user_id'];

    public function person() {
        return $this->belongsTo(Person::class);
    }

    // public function purchases() {
    //     return $this->hasMany(PlotSale::class);
    // }

    // public function plotSales()
    // {
    //     return $this->hasMany(PlotSale::class);
    // }
    
    // public function payments()
    // {
    //     return $this->hasMany(Payment::class);
    // }
    
    // public function creditNotes()
    // {
    //     return $this->hasMany(CreditNote::class);
    // }
    
    // public function ownedPlots()
    // {
    //     return $this->hasMany(Plot::class, 'current_owner_id');
    // }
    
    // public function financialTransactions()
    // {
    //     return $this->hasMany(FinancialTransaction::class);
    // }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    // public function relation()
    // {
    //     return $this->belongsTo(Relation::class);
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
}
