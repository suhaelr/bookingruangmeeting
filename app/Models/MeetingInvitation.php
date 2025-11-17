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
        'attendance_status',
        'attendance_confirmed_at',
        'attendance_declined_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
        'attendance_confirmed_at' => 'datetime',
        'attendance_declined_at' => 'datetime',
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

    /**
     * Konfirmasi kehadiran PIC
     */
    public function confirmAttendance()
    {
        $this->update([
            'attendance_status' => 'confirmed',
            'attendance_confirmed_at' => now(),
        ]);
    }

    /**
     * PIC tidak bisa hadir
     */
    public function declineAttendance()
    {
        $this->update([
            'attendance_status' => 'declined',
            'attendance_declined_at' => now(),
        ]);
    }

    /**
     * Mark sebagai tidak hadir (auto setelah meeting lewat)
     */
    public function markAsAbsent()
    {
        $this->update([
            'attendance_status' => 'absent',
        ]);
    }

    /**
     * Cek apakah attendance masih pending
     */
    public function isAttendancePending()
    {
        return $this->attendance_status === 'pending';
    }

    /**
     * Cek apakah attendance sudah dikonfirmasi
     */
    public function isAttendanceConfirmed()
    {
        return $this->attendance_status === 'confirmed';
    }

    /**
     * Cek apakah attendance ditolak
     */
    public function isAttendanceDeclined()
    {
        return $this->attendance_status === 'declined';
    }

    /**
     * Cek apakah attendance absent
     */
    public function isAttendanceAbsent()
    {
        return $this->attendance_status === 'absent';
    }

    /**
     * Get warna untuk kalender berdasarkan attendance status
     */
    public function getAttendanceStatusColor()
    {
        return match($this->attendance_status) {
            'confirmed' => 'green',
            'declined', 'absent' => 'red',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get text status attendance
     */
    public function getAttendanceStatusText()
    {
        return match($this->attendance_status) {
            'confirmed' => 'Dikonfirmasi akan hadir',
            'declined' => 'Belum bisa hadir',
            'absent' => 'Tidak hadir',
            'pending' => 'Belum ada respon',
            default => 'Tidak diketahui',
        };
    }
}