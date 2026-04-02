<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index() {
        $todayString = Carbon::today()->toDateString();
        $twoDaysAheadString = Carbon::today()->addDays(2)->toDateString();

        // Overdue: past delivery date, not completed
        $overdueOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '<', $todayString)
            ->orderBy('delivery_date')
            ->get();

        // Today's deliveries
        $deliveriesToday = Order::with(['client', 'itemCategory'])
            ->whereNotNull('delivery_date')
            ->where('delivery_date', 'like', $todayString . '%')
            ->where('status', '!=', 'completed')
            ->get();

        // Upcoming (tomorrow + day after)
        $upcomingDeliveries = Order::with(['client', 'itemCategory'])
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '>', $todayString . ' 23:59:59')
            ->where('delivery_date', '<=', $twoDaysAheadString . ' 23:59:59')
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
