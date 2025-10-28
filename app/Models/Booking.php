<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meeting_room_id',
        'title',
        'description',
        'description_visibility',
        'start_time',
        'end_time',
        'status',
        'attendees_count',
        'attendees',
        'attachments',
        'special_requirements',
        'unit_kerja',
        'dokumen_perizinan',
        'total_cost',
        'cancelled_at',
        'cancellation_reason',
        'preempt_status',
        'preempt_requested_by',
        'preempt_deadline_at',
        'preempt_reason',
        'needs_reschedule',
        'reschedule_deadline_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'attendees' => 'array',
        'attachments' => 'array',
        'cancelled_at' => 'datetime',
        'total_cost' => 'decimal:2',
        'preempt_deadline_at' => 'datetime',
        'reschedule_deadline_at' => 'datetime',
        'needs_reschedule' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meetingRoom()
    {
        return $this->belongsTo(MeetingRoom::class);
    }

    public function getDurationAttribute()
    {
        return $this->start_time->diffInHours($this->end_time);
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time->format('d M Y, H:i');
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time->format('d M Y, H:i');
    }

    public function isUpcoming()
    {
        return $this->start_time > now() && $this->status === 'confirmed';
    }

    public function isPast()
    {
        return $this->end_time < now();
    }

    public function isOngoing()
    {
        return $this->start_time <= now() && $this->end_time >= now() && $this->status === 'confirmed';
    }

    public function canBeCancelled()
    {
        return $this->status === 'pending' || 
               ($this->status === 'confirmed' && $this->start_time > now()->addHours(2));
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'cancelled' => 'red',
            'completed' => 'blue',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'cancelled' => 'Dibatalkan',
            'completed' => 'Selesai',
            default => 'Tidak Diketahui'
        };
    }

    public function calculateTotalCost()
    {
        // Since hourly_rate has been removed, return 0 for now
        // This can be updated later if pricing is needed
        return 0.00;
    }

    public function isPreemptPending(): bool
    {
        return $this->preempt_status === 'pending';
    }

    public function startPreempt(int $requesterUserId, \DateTimeInterface $deadlineAt, ?string $reason = null): void
    {
        $this->preempt_status = 'pending';
        $this->preempt_requested_by = $requesterUserId;
        $this->preempt_deadline_at = $deadlineAt;
        $this->preempt_reason = $reason;
        $this->save();
    }

    public function closePreempt(): void
    {
        $this->preempt_status = 'closed';
        $this->preempt_requested_by = null;
        $this->preempt_deadline_at = null;
        $this->preempt_reason = null;
        $this->save();
    }

    public function invitations()
    {
        return $this->hasMany(MeetingInvitation::class);
    }

    public function invitedPics()
    {
        return $this->belongsToMany(User::class, 'meeting_invitations', 'booking_id', 'pic_id')
            ->withPivot(['status', 'invited_at', 'responded_at'])
            ->withTimestamps();
    }

    public function updateStatus($status, $reason = null)
    {
        try {
            $this->status = $status;
            
            if ($status === 'cancelled') {
                $this->cancelled_at = now();
                $this->cancellation_reason = $reason;
            }
            
            $this->save();
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error updating booking status: ' . $e->getMessage());
            throw $e;
        }
    }
}
