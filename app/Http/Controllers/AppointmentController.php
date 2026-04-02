<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // ?week=0 = this week starting TODAY, ?week=1 = next 7 days, etc.
        $weekOffset = (int) $request->get('week', 0);

        // Start from TODAY (not last Monday) so users always see upcoming orders
        $weekStart = \Carbon\Carbon::today()->addWeeks($weekOffset);
        $weekEnd   = $weekStart->copy()->addDays(6)->endOfDay();

        $weekStartString = $weekStart->toDateString();
        $weekEndString   = $weekEnd->toDateString();
        $todayString     = \Carbon\Carbon::today()->toDateString();

        $weekOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '>=', $weekStartString)
            ->where('delivery_date', '<=', $weekEndString . ' 23:59:59')
            ->orderBy('delivery_date')
            ->get();

        $overdueOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('delivery_date')
            ->where('delivery_date', '<', $todayString)
            ->orderBy('delivery_date')
            ->get();

        return view('appointments.index', compact(
            'weekOrders', 'overdueOrders', 'weekStart', 'weekEnd', 'weekOffset'
        ));
    }
}
