<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('orders')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $clients = $query->paginate(12)->withQueryString();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:30',
            'is_traveler' => 'nullable|boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        $data['is_traveler'] = $request->boolean('is_traveler');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('clients', 'public');
        }

        $client = Client::create($data);

        return redirect()->route('clients.show', $client)
                         ->with('success', 'تم إضافة العميلة بنجاح! 🌟');
    }

    public function show(Client $client)
    {
        $client->load(['orders.itemCategory']);
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:30',
            'is_traveler' => 'nullable|boolean',
            'image'       => 'nullable|image|max:4096',
        ]);

        $data['is_traveler'] = $request->boolean('is_traveler');

        if ($request->hasFile('image')) {
            if ($client->image) {
                Storage::disk('public')->delete($client->image);
            }
            $data['image'] = $request->file('image')->store('clients', 'public');
        }

        $client->update($data);

        return redirect()->route('clients.show', $client)
                         ->with('success', 'تم تعديل بيانات العميلة بنجاح! ✏️');
    }

    public function destroy(Client $client)
    {
        if ($client->image) {
            Storage::disk('public')->delete($client->image);
        }
        $client->delete();

        return redirect()->route('clients.index')
                         ->with('success', 'تم حذف العميلة.');
    }
}
