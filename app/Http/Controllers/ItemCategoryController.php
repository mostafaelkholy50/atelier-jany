<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::all();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'measurements' => 'nullable|array',
        ]);

        $category = ItemCategory::create([
            'name' => $validated['name'],
            'default_measurements' => $validated['measurements'] ?? [],
        ]);

        \Illuminate\Support\Facades\Cache::forget('item_categories_all');

        if ($request->wantsJson()) {
            return response()->json($category);
        }
        return redirect()->back();
    }

    public function update(Request $request, ItemCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'measurements' => 'nullable|array',
        ]);

        $category->update([
            'name' => $validated['name'],
            'default_measurements' => $validated['measurements'] ?? [],
        ]);

        \Illuminate\Support\Facades\Cache::forget('item_categories_all');

        if ($request->wantsJson()) {
            return response()->json($category);
        }
        return redirect()->back();
    }

    public function destroy(ItemCategory $category)
    {
        $category->delete();
        
        \Illuminate\Support\Facades\Cache::forget('item_categories_all');
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back();
    }
}
