<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasWhatsappNumber extends Model
{
    use HasFactory;

    protected $table = 'mas_whatsapp_numbers';

    protected $fillable = [
        'name',
        'number',
    ];

    /**
     * Format the WhatsApp number for display.
     */
    public function getFormattedNumberAttribute(): string
    {
        $number = $this->number;
        
        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Add country code if not present
        if (!str_starts_with($number, '62')) {
            if (str_starts_with($number, '0')) {
                $number = '62' . substr($number, 1);
            } else {
                $number = '62' . $number;
            }
        }
        
        return '+' . $number;
    }

    /**
     * Get the WhatsApp URL for this number.
     */
    public function getWhatsappUrlAttribute(): string
    {
        $number = preg_replace('/[^0-9]/', '', $this->number);
        
        // Add country code if not present
        if (!str_starts_with($number, '62')) {
            if (str_starts_with($number, '0')) {
                $number = '62' . substr($number, 1);
            } else {
                $number = '62' . $number;
            }
        }
        
        return 'https://wa.me/' . $number;
    }

    /**
     * Scope to search by name or number.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('number', 'like', "%{$search}%");
        });
    }

    /**
     * Validate Indonesian phone number format.
     */
    public static function validateIndonesianNumber($number): bool
    {
        // Remove non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);
        
        // Check if it's a valid Indonesian mobile number
        // Should start with 08, 628, or 62 followed by 8
        return preg_match('/^(08|628|62[0-9])[0-9]{8,12}$/', $number);
    }
}