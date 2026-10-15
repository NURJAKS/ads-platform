<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'ads_total'     => Ad::count(),
            'ads_pending'   => Ad::where('status', 'pending')->count(),
            'ads_approved'  => Ad::where('status', 'approved')->count(),
            'ads_rejected'  => Ad::where('status', 'rejected')->count(),
            'users_total'   => User::count(),
            'users_admins'  => User::where('role', 'admin')->count(),
        ];

        $latestPending = Ad::with(['user', 'category', 'images'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestPending'));
    }
}

