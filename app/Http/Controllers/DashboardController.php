<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get user statistics
        $totalUsers = User::count();
        $adminUsers = User::where('role', 'admin')->count();
        $gestionnaireUsers = User::where('role', 'gestionnaire_archives')->count();
        $serviceUsers = User::where('role', 'service_producteurs')->count();
        
        // Get recent users (last 30 days)
        $recentUsers = User::where('created_at', '>=', now()->subDays(30))
                                 ->orderBy('created_at', 'desc')
                                 ->limit(5)
                                 ->get();
        
        // Get users by role for chart
        $usersByRole = [
            'admin' => $adminUsers,
            'gestionnaire_archives' => $gestionnaireUsers,
            'service_producteurs' => $serviceUsers,
        ];
        
        // Get monthly user registrations for the last 6 months
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                               ->whereMonth('created_at', $date->month)
                               ->count();
            
            $monthlyRegistrations[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        
        // System stats (placeholder - you can add real data later)
        $systemStats = [
            'total_documents' => 0, // Placeholder
            'total_archives' => 0,  // Placeholder
            'pending_requests' => 0, // Placeholder
            'storage_used' => '0 GB', // Placeholder
        ];
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'adminUsers',
            'gestionnaireUsers',
            'serviceUsers',
            'recentUsers',
            'usersByRole',
            'monthlyRegistrations',
            'systemStats'
        ));
    }
}