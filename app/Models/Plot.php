<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;
    public $timestamps = false;

     protected $fillable = ['status'];

    protected $casts = [
        'width' => 'double', // or 'float' or 'decimal:2' for specific precision
        'length' => 'double',
    ];

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        return "Block {$this->block}, Sector {$this->sector}";
    }

    public function getSizeAttribute()
    {
        return "{$this->width} x {$this->length} ft";
    }

    public function getMarlaAttribute()
    {
        $marla =  (double) number_format(($this->width * $this->length) / 272, 13);
        return $marla;
    }

    // Methods

     public function category()
    {
        return $this->belongsTo(PlotCategory::class);
    }

    public function sales()
    {
        return $this->hasMany(PlotSale::class);
    }

      /**
     * Get the active plot sale for this plot
     */
    public function activePlotSale()
    {
        return $this->hasOne(PlotSale::class)
            ->where('booking_status', 'active')
            ->latest('booking_date');
    }
    
    public function isAvailable()
    {
        return $this->status === 'available';
    }

    public function isBooked()
    {
        return $this->status === 'booked';
    }

    public function isSold()
    {
        return $this->status === 'sold';
    }
}
