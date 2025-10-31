<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingInvitation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'pic_id',
        'invited_by_pic_id',
        'status',
        'invited_at',
        'responded_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function invitedByPic()
    {
        return $this->belongsTo(User::class, 'invited_by_pic_id');
    }

    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    public function decline()
    {
        $this->update([
            'status' => 'declined',
            'responded_at' => now(),
        ]);
    }
}