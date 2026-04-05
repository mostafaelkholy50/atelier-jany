<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['client', 'itemCategory'])->orderBy('order_date', 'desc')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $orders = $query->paginate(10)->withQueryString();
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $clients = \App\Models\Client::select('id', 'name', 'phone', 'is_traveler', 'image')->get();
        $categories = \Illuminate\Support\Facades\Cache::remember('item_categories_all', 86400, fn() => \App\Models\ItemCategory::all());
        return view('orders.create', compact('clients', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_traveler' => 'boolean',
            'client_image' => 'nullable|image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.item_category_id' => 'required|exists:item_categories,id',
            'items.*.fabric_color' => 'nullable|string',
            'items.*.measurements' => 'nullable|array',
            'items.*.design_image' => 'nullable|image|max:2048',
            'items.*.total_price' => 'required|numeric',
            'items.*.deposit' => 'nullable|numeric',
            'items.*.notes' => 'nullable|string',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
        ]);

        if (!empty($validated['phone'])) {
            $client = Client::firstOrCreate(
                ['phone' => $validated['phone']],
                ['name' => $validated['name']]
            );
        } else {
            $client = Client::firstOrCreate(
                ['name' => $validated['name']]
            );
        }
        
        $client->is_traveler = $request->boolean('is_traveler');
        $client->save();

        if ($request->hasFile('client_image')) {
            $clientImage = $request->file('client_image')->store('clients', 'public');
            $client->update(['image' => $clientImage]);
        }

        foreach ($request->items as $index => $itemData) {
            $orderCode = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            while (Order::where('order_code', $orderCode)->exists()) {
                $orderCode = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }

            $designImage = null;
            if ($request->hasFile("items.{$index}.design_image")) {
                $designImage = $request->file("items.{$index}.design_image")->store('designs', 'public');
            }

            Order::create([
                'client_id' => $client->id,
                'order_code' => $orderCode,
                'item_category_id' => $itemData['item_category_id'],
                'fabric_color' => $itemData['fabric_color'] ?? null,
                'measurements' => $itemData['measurements'] ?? null,
                'design_image' => $designImage,
                'total_price' => $itemData['total_price'],
                'deposit' => $itemData['deposit'] ?? 0,
                'is_fully_paid' => $itemData['total_price'] <= ($itemData['deposit'] ?? 0),
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'status' => 'pending',
                'notes' => $itemData['notes'] ?? null,
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'تم إضافة الطلب بنجاح! تقدري تشوفيه في الجدول دلوقتي.');
    }

    public function show(Order $order)
    {
        $order->load(['client', 'itemCategory']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['client', 'itemCategory']);
        $clients = \App\Models\Client::select('id', 'name', 'phone', 'is_traveler', 'image')->get();
        $categories = \Illuminate\Support\Facades\Cache::remember('item_categories_all', 86400, fn() => \App\Models\ItemCategory::all());
        return view('orders.edit', compact('order', 'clients', 'categories'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'is_traveler' => 'boolean', // added
            'item_category_id' => 'required|exists:item_categories,id',
            'fabric_color' => 'nullable|string',
            'measurements' => 'nullable|array',
            'total_price' => 'required|numeric',
            'deposit' => 'nullable|numeric',
            'order_date' => 'required|date',
            'delivery_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('design_image')) {
            $validated['design_image'] = $request->file('design_image')->store('designs', 'public');
        }

        $validated['is_fully_paid'] = $validated['total_price'] <= ($validated['deposit'] ?? 0);
        $validated['deposit'] = $validated['deposit'] ?? 0;

        $order->update($validated);
        $order->client->update(['is_traveler' => $request->boolean('is_traveler')]);

        return redirect()->route('orders.index')->with('success', 'تم تحديث بيانات الطلب بنجاح.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'تم حذف الطلب بنجاح.');
    }

    public function toggleStatus(Request $request, Order $order)
    {
        if ($request->has('status')) {
            $order->status = $request->status; // e.g., 'completed' or 'pending'
        }
        if ($request->has('is_fully_paid')) {
            $order->is_fully_paid = $request->boolean('is_fully_paid');
            // If toggling back to Remaining, we don't automatically clear current deposit, 
            // but we ensure is_fully_paid is false. 
            // If toggling to Paid, we match deposit to total_price.
            if ($order->is_fully_paid) {
                $order->deposit = $order->total_price;
            } else {
                // If they untick "Paid", it means they don't want it marked as "Paid" anymore.
                // We shouldn't necessarily zero out the deposit (they might have paid PART of it), 
                // but the UI currently ONLY allows toggling to "Full". 
                // Let's just set it to false and let them edit it in the edit page if they want specific values.
                // Or we can keep the current deposit but mark is_fully_paid = false.
            }
        }
        $order->save();

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'order' => $order]);
        }
        return back();
    }
}
