<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index() {
        $today       = Carbon::today();
        $twoDaysAhead = Carbon::today()->addDays(2);

        // Overdue: past delivery date, not completed
        $overdueOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->whereDate('delivery_date', '<', $today)
            ->orderBy('delivery_date')
            ->get();

        // Today's deliveries
        $deliveriesToday = Order::with(['client', 'itemCategory'])
            ->whereDate('delivery_date', $today)
            ->where('status', '!=', 'completed')
            ->get();

        // Upcoming (tomorrow + day after)
        $upcomingDeliveries = Order::with(['client', 'itemCategory'])
            ->whereDate('delivery_date', '>', $today)
            ->whereDate('delivery_date', '<=', $twoDaysAhead)
            ->where('status', '!=', 'completed')
            ->orderBy('delivery_date')
            ->get();

        $pendingOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->orderBy('delivery_date')
            ->limit(5)
            ->get();

        $totalOrders   = Order::count();
        $totalClients  = \App\Models\Client::count();
        $totalRevenue  = Order::sum('total_price');

        return view('dashboard', compact(
            'overdueOrders', 'deliveriesToday', 'upcomingDeliveries',
            'pendingOrders', 'totalOrders', 'totalClients', 'totalRevenue'
        ));
    }
}
