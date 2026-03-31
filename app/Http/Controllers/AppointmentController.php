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

        // Fetch all active orders to bypass SQLite date casting bugs, then filter in memory
        $activeOrders = Order::with(['client', 'itemCategory'])
            ->where('status', '!=', 'completed')
            ->whereNotNull('delivery_date')
            ->orderBy('delivery_date')
            ->get();

        $weekOrders = $activeOrders->filter(function ($o) use ($weekStart, $weekEnd) {
            $d = \Carbon\Carbon::parse($o->delivery_date)->startOfDay();
            return $d->between($weekStart->copy()->startOfDay(), $weekEnd->copy()->endOfDay());
        });

        $overdueOrders = $activeOrders->filter(function ($o) {
            return \Carbon\Carbon::parse($o->delivery_date)->endOfDay()->isPast();
        });

        return view('appointments.index', compact(
            'weekOrders', 'overdueOrders', 'weekStart', 'weekEnd', 'weekOffset'
        ));
    }
}
