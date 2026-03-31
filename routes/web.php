<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');
// Independent storage access for when symlinks fail (e.g. some shared hosts or XAMPP)
Route::get('/app-storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('storage.direct');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('orders', OrderController::class);
    Route::patch('/orders/{order}/toggle', [OrderController::class, 'toggleStatus'])->name('orders.toggle');

    Route::resource('categories', ItemCategoryController::class);

    Route::resource('clients', ClientController::class);
    Route::get('/measurements', function () { return view('measurements.index'); })->name('measurements.index');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/designs', function () { return view('designs.index'); })->name('designs.index');
    Route::get('/payments', function () { return view('payments.index'); })->name('payments.index');
    Route::get('/reports', function () { return view('reports.index'); })->name('reports.index');

    Route::get('/api/categories/{id}/measurements', function ($id) {
        $category = ItemCategory::findOrFail($id);
        return response()->json($category->default_measurements ?? []);
    });
});

require __DIR__ . '/auth.php';
