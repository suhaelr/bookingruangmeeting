<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'amenities',
        'location',
        'hourly_rate',
        'images',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getActiveBookings()
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->get();
    }

    public function isAvailable($startTime, $endTime, $excludeBookingId = null)
    {
        $query = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->count() === 0;
    }

    public function getBookingStats()
    {
        return [
            'total_bookings' => $this->bookings()->count(),
            'this_month' => $this->bookings()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'revenue' => $this->bookings()
                ->where('status', 'confirmed')
                ->sum('total_cost'),
        ];
    }

    public function getAmenitiesList()
    {
        return $this->amenities ?? [];
    }

    public function getImagesList()
    {
        return $this->images ?? [];
    }
}
