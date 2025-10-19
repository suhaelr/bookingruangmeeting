<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Export booking data to CSV (Excel compatible)
     */
    public function exportBookingsExcel(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDay()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get bookings data with relationships
        $bookings = Booking::with(['user', 'meetingRoom'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get room availability data
        $rooms = MeetingRoom::with(['bookings' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }])->get();
        
        $filename = 'booking_data_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        return response()->stream(function() use ($bookings, $rooms, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Title
            fputcsv($handle, ['LAPORAN DATA BOOKING & KETERSEDIAAN RUANG MEETING']);
            fputcsv($handle, ['Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y')]);
            fputcsv($handle, ['Dibuat pada: ' . Carbon::now()->format('d M Y H:i:s')]);
            fputcsv($handle, []); // Empty line
            
            // Bookings Data Section
            fputcsv($handle, ['DATA RIWAYAT BOOKING']);
            fputcsv($handle, [
                'No',
                'Judul Meeting',
                'Nama Pemesan',
                'Unit Kerja',
                'Email',
                'Ruang Meeting',
                'Tanggal & Waktu Mulai',
                'Tanggal & Waktu Selesai',
                'Status',
                'Kontak (Email)'
            ]);
            
            $no = 1;
            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $no++,
                    $booking->title,
                    $booking->user->full_name ?? $booking->user->name ?? 'N/A',
                    $booking->user->unit_kerja ?? 'N/A',
                    $booking->user->email,
                    $booking->meetingRoom->name,
                    $booking->start_time->format('d M Y H:i'),
                    $booking->end_time->format('d M Y H:i'),
                    ucfirst($booking->status),
                    $booking->user->email
                ]);
            }
            
            fputcsv($handle, []); // Empty line
            
            // Room Availability Section
            fputcsv($handle, ['STATUS KETERSEDIAAN RUANG']);
            fputcsv($handle, [
                'No',
                'Nama Ruang',
                'Lokasi',
                'Kapasitas',
                'Total Booking (24h)',
                'Status Ketersediaan',
                'Detail Booking'
            ]);
            
            $no = 1;
            foreach ($rooms as $room) {
                $bookingCount = $room->bookings->count();
                $availability = $bookingCount > 0 ? 'Terisi' : 'Kosong';
                $bookingDetails = '';
                
                if ($room->bookings->count() > 0) {
                    $details = [];
                    foreach ($room->bookings as $booking) {
                        $details[] = $booking->user->full_name . ' (' . $booking->start_time->format('H:i') . '-' . $booking->end_time->format('H:i') . ')';
                    }
                    $bookingDetails = implode('; ', $details);
                } else {
                    $bookingDetails = 'Tidak ada booking';
                }
                
                fputcsv($handle, [
                    $no++,
                    $room->name,
                    $room->location,
                    $room->capacity . ' orang',
                    $bookingCount,
                    $availability,
                    $bookingDetails
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }
    
    /**
     * Export booking data to HTML (PDF compatible)
     */
    public function exportBookingsPDF(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDay()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get bookings data with relationships
        $bookings = Booking::with(['user', 'meetingRoom'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get room availability data
        $rooms = MeetingRoom::with(['bookings' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }])->get();
        
        $data = [
            'bookings' => $bookings,
            'rooms' => $rooms,
            'startDate' => Carbon::parse($startDate)->format('d M Y'),
            'endDate' => Carbon::parse($endDate)->format('d M Y'),
            'generatedAt' => Carbon::now()->format('d M Y H:i:s')
        ];
        
        $filename = 'booking_data_' . $startDate . '_to_' . $endDate . '.html';
        
        return response()->view('admin.exports.booking-report', $data)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
