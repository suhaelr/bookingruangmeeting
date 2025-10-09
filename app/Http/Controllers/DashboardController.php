<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Sample data untuk tabel
        $data = [
            [
                'id' => '#001',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'status' => 'Active',
                'created' => '2024-01-15',
            ],
            [
                'id' => '#002',
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'status' => 'Pending',
                'created' => '2024-01-14',
            ],
            [
                'id' => '#003',
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'status' => 'Inactive',
                'created' => '2024-01-13',
            ],
            [
                'id' => '#004',
                'name' => 'Alice Brown',
                'email' => 'alice@example.com',
                'status' => 'Active',
                'created' => '2024-01-12',
            ],
            [
                'id' => '#005',
                'name' => 'Charlie Wilson',
                'email' => 'charlie@example.com',
                'status' => 'New',
                'created' => '2024-01-11',
            ],
        ];

        return view('dashboard', compact('data'));
    }
}
