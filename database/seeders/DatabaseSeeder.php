<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MeetingRoom;
use App\Models\Booking;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Super Administrator',
            'username' => 'admin',
            'email' => 'admin@jadixpert.com',
            'password' => Hash::make('admin'),
            'full_name' => 'Super Administrator',
            'phone' => '081234567890',
            'department' => 'IT',
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'username' => 'user',
            'email' => 'user@jadixpert.com',
            'password' => Hash::make('user'),
            'full_name' => 'Regular User',
            'phone' => '081234567891',
            'department' => 'General',
            'role' => 'user',
        ]);

        // Create additional users
        User::create([
            'name' => 'John Doe',
            'username' => 'john.doe',
            'email' => 'john.doe@jadixpert.com',
            'password' => Hash::make('password'),
            'full_name' => 'John Doe',
            'phone' => '081234567892',
            'department' => 'Marketing',
            'role' => 'user',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'username' => 'jane.smith',
            'email' => 'jane.smith@jadixpert.com',
            'password' => Hash::make('password'),
            'full_name' => 'Jane Smith',
            'phone' => '081234567893',
            'department' => 'HR',
            'role' => 'user',
        ]);

        // Create meeting rooms
        $rooms = [
            [
                'name' => 'Conference Room A',
                'description' => 'Large conference room with modern facilities',
                'capacity' => 20,
                'amenities' => ['projector', 'whiteboard', 'wifi', 'ac', 'sound_system'],
                'location' => 'Floor 1, Building A',
                'images' => ['room1.jpg', 'room1_2.jpg'],
                'is_active' => true,
            ],
            [
                'name' => 'Meeting Room B',
                'description' => 'Medium-sized meeting room for small groups',
                'capacity' => 8,
                'amenities' => ['whiteboard', 'wifi', 'ac'],
                'location' => 'Floor 2, Building A',
                'images' => ['room2.jpg'],
                'is_active' => true,
            ],
            [
                'name' => 'Executive Boardroom',
                'description' => 'Premium boardroom for executive meetings',
                'capacity' => 12,
                'amenities' => ['projector', 'whiteboard', 'wifi', 'ac', 'video_conference', 'catering'],
                'location' => 'Floor 3, Building A',
                'images' => ['room3.jpg', 'room3_2.jpg'],
                'is_active' => true,
            ],
            [
                'name' => 'Training Room',
                'description' => 'Large training room with presentation facilities',
                'capacity' => 30,
                'amenities' => ['projector', 'whiteboard', 'wifi', 'ac', 'sound_system', 'microphone'],
                'location' => 'Floor 1, Building B',
                'images' => ['room4.jpg'],
                'is_active' => true,
            ],
            [
                'name' => 'Small Meeting Room',
                'description' => 'Intimate meeting room for 2-4 people',
                'capacity' => 4,
                'amenities' => ['wifi', 'ac'],
                'location' => 'Floor 2, Building B',
                'images' => ['room5.jpg'],
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $roomData) {
            MeetingRoom::create($roomData);
        }

        // Create sample bookings
        $users = User::where('role', 'user')->get();
        $meetingRooms = MeetingRoom::all();

        // Create bookings for the past week
        for ($i = 0; $i < 10; $i++) {
            $user = $users->random();
            $room = $meetingRooms->random();
            $startTime = Carbon::now()->subDays(rand(0, 7))->addHours(rand(9, 17));
            $endTime = $startTime->copy()->addHours(rand(1, 3));
            
            $statuses = ['pending', 'confirmed', 'cancelled'];
            $status = $statuses[array_rand($statuses)];

            Booking::create([
                'user_id' => $user->id,
                'meeting_room_id' => $room->id,
                'title' => 'Meeting ' . ($i + 1),
                'description' => 'Sample meeting description for booking ' . ($i + 1),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'attendees_count' => rand(2, 10),
                'attendees' => ['user1@example.com', 'user2@example.com'],
                'special_requirements' => $i % 3 === 0 ? 'Need projector and whiteboard' : null,
                'total_cost' => 0.00, // hourly_rate removed
                'cancelled_at' => $status === 'cancelled' ? now() : null,
                'cancellation_reason' => $status === 'cancelled' ? 'Meeting cancelled due to schedule conflict' : null,
            ]);
        }

        // Create future bookings
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $room = $meetingRooms->random();
            $startTime = Carbon::now()->addDays(rand(1, 30))->addHours(rand(9, 17));
            $endTime = $startTime->copy()->addHours(rand(1, 4));
            
            $statuses = ['pending', 'confirmed'];
            $status = $statuses[array_rand($statuses)];

            Booking::create([
                'user_id' => $user->id,
                'meeting_room_id' => $room->id,
                'title' => 'Future Meeting ' . ($i + 1),
                'description' => 'Sample future meeting description for booking ' . ($i + 1),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $status,
                'attendees_count' => rand(2, 15),
                'attendees' => ['future1@example.com', 'future2@example.com'],
                'special_requirements' => $i % 4 === 0 ? 'Need video conference setup' : null,
                'total_cost' => 0.00, // hourly_rate removed
            ]);
        }
    }
}