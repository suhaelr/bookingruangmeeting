<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExportController extends Controller
{
    /**
     * Export booking data to Excel format
     */
    public function exportBookingsExcel(Request $request)
    {
        try {
            // Get all bookings (not filtered by date for admin export)
            $bookings = Booking::with(['user', 'meetingRoom'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set sheet title
            $sheet->setTitle('Data Booking');

            // Title
            $sheet->setCellValue('A1', 'LAPORAN DATA BOOKING RUANG MEETING');
            $sheet->mergeCells('A1:N1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Dibuat pada: ' . Carbon::now()->format('d M Y H:i:s'));
            $sheet->mergeCells('A2:N2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Headers
            $headers = [
                'No',
                'ID Booking',
                'Judul Meeting',
                'Nama Pemesan',
                'Unit Kerja',
                'Email',
                'Ruang Meeting',
                'Lokasi Ruang',
                'Tanggal & Waktu Mulai',
                'Tanggal & Waktu Selesai',
                'Durasi (Jam)',
                'Status',
                'Peserta',
                'Deskripsi'
            ];

            $row = 4;
            $col = 1;
            foreach ($headers as $header) {
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellAddress = $columnLetter . $row;
                $sheet->setCellValue($cellAddress, $header);
                $sheet->getStyle($cellAddress)->getFont()->setBold(true);
                $sheet->getStyle($cellAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF4472C4');
                $sheet->getStyle($cellAddress)->getFont()->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($cellAddress)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $col++;
            }

            // Data rows
            $row = 5;
            $no = 1;
            foreach ($bookings as $booking) {
                $startTime = Carbon::parse($booking->start_time);
                $endTime = Carbon::parse($booking->end_time);
                $duration = $startTime->diffInHours($endTime);

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $booking->id);
                $sheet->setCellValue('C' . $row, $booking->title);
                $sheet->setCellValue('D' . $row, $booking->user->full_name ?? $booking->user->name ?? 'N/A');
                $sheet->setCellValue('E' . $row, $booking->user->unit_kerja ?? 'N/A');
                $sheet->setCellValue('F' . $row, $booking->user->email);
                $sheet->setCellValue('G' . $row, $booking->meetingRoom->name);
                $sheet->setCellValue('H' . $row, $booking->meetingRoom->location);
                $sheet->setCellValue('I' . $row, $startTime->format('d M Y H:i'));
                $sheet->setCellValue('J' . $row, $endTime->format('d M Y H:i'));
                $sheet->setCellValue('K' . $row, $duration);
                $sheet->setCellValue('L' . $row, ucfirst($booking->status));
                $sheet->setCellValue('M' . $row, $booking->attendees_count ?? 0);
                $sheet->setCellValue('N' . $row, $booking->description ?? '-');

                // Auto-size columns
                $sheet->getColumnDimension('A')->setAutoSize(true);
                $sheet->getColumnDimension('B')->setAutoSize(true);
                $sheet->getColumnDimension('C')->setAutoSize(true);
                $sheet->getColumnDimension('D')->setAutoSize(true);
                $sheet->getColumnDimension('E')->setAutoSize(true);
                $sheet->getColumnDimension('F')->setAutoSize(true);
                $sheet->getColumnDimension('G')->setAutoSize(true);
                $sheet->getColumnDimension('H')->setAutoSize(true);
                $sheet->getColumnDimension('I')->setAutoSize(true);
                $sheet->getColumnDimension('J')->setAutoSize(true);
                $sheet->getColumnDimension('K')->setAutoSize(true);
                $sheet->getColumnDimension('L')->setAutoSize(true);
                $sheet->getColumnDimension('M')->setAutoSize(true);
                $sheet->getColumnDimension('N')->setAutoSize(true);

                $row++;
            }

            // Freeze header row
            $sheet->freezePane('A5');

            // Set filename
            $filename = 'bookings-export-' . Carbon::now()->format('Y-m-d-His') . '.xlsx';

            // Create writer and save to temp file
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer->save($tempFile);

            // Return Excel file as download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Failed to export bookings to Excel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
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
        $rooms = MeetingRoom::with(['bookings' => function ($query) use ($startDate, $endDate) {
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
