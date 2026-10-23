<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarwashApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

// Guest Web Views
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/sign-up', function () {
    return view('sign-up');
});

// Authenticated Web Views (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    Route::get('/bookings', function () {
        return view('bookings');
    });
    Route::get('/crm', function () {
        return view('crm');
    });
    Route::get('/staff', function () {
        return view('staff');
    });
    Route::get('/inventory', function () {
        return view('inventory');
    });
    Route::get('/jobcards', function () {
        return view('jobcards');
    });
    Route::get('/payments', function () {
        return view('payments');
    });
    Route::get('/vehicles', function () {
        return redirect('/crm');
    });
    Route::get('/services', function () {
        return view('services');
    });
    Route::get('/queue', function () {
        return view('queue');
    });
    Route::get('/display', function () {
        return view('display');
    });
    Route::get('/branches', function () {
        return view('branches');
    });
    Route::get('/settings', function () {
        return view('settings');
    });
    Route::get('/reports', function () {
        return view('reports');
    });
    Route::get('/expenses', function () {
        return view('expenses');
    });
    
    // Manager Portal
    Route::get('/manager', function () {
        return view('manager');
    });
});

// API Routes
Route::prefix('api')->group(function () {
    // Auth routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // SaaS features routes
    Route::get('/global', [CarwashApiController::class, 'getGlobalData']);
    Route::post('/branches', [CarwashApiController::class, 'createBranch']);
    Route::get('/branches', [CarwashApiController::class, 'getBranches']);
    Route::put('/branches/{id}', [CarwashApiController::class, 'updateBranch']);
    Route::delete('/branches/{id}', [CarwashApiController::class, 'deleteBranch']);
    Route::get('/dashboard/stats', [CarwashApiController::class, 'getDashboardStats']);
    Route::get('/reports/data', [CarwashApiController::class, 'getReportsData']);
    Route::get('/bookings', [CarwashApiController::class, 'getBookings']);
    Route::post('/bookings', [CarwashApiController::class, 'createBooking']);
    Route::put('/bookings/{id}', [CarwashApiController::class, 'updateBooking']);
    Route::delete('/bookings/{id}', [CarwashApiController::class, 'deleteBooking']);
    Route::get('/customers', [CarwashApiController::class, 'getCustomers']);
    Route::post('/customers', [CarwashApiController::class, 'createCustomer']);
    Route::put('/customers/{id}', [CarwashApiController::class, 'updateCustomer']);
    Route::delete('/customers/{id}', [CarwashApiController::class, 'deleteCustomer']);

    Route::get('/vehicles', [CarwashApiController::class, 'getVehicles']);
    Route::put('/vehicles/{id}', [CarwashApiController::class, 'updateVehicle']);
    Route::delete('/vehicles/{id}', [CarwashApiController::class, 'deleteVehicle']);
    Route::get('/services', [CarwashApiController::class, 'getServices']);
    Route::get('/loyalty', [CarwashApiController::class, 'getLoyalty']);

    Route::post('/services', [CarwashApiController::class, 'createService']);
    Route::put('/services/{id}', [CarwashApiController::class, 'updateService']);
    Route::delete('/services/{id}', [CarwashApiController::class, 'deleteService']);

    Route::get('/staff', [CarwashApiController::class, 'getStaff']);
    Route::post('/staff', [CarwashApiController::class, 'createStaff']);
    Route::put('/staff/{id}', [CarwashApiController::class, 'updateStaff']);
    Route::post('/staff/attendance/bulk', [CarwashApiController::class, 'bulkUpdateAttendance']);
    Route::get('/staff/{id}/attendance', [CarwashApiController::class, 'getStaffMonthlyAttendance']);
    Route::post('/staff/{id}/photo', [CarwashApiController::class, 'uploadStaffPhoto']);
    Route::post('/staff/{id}/document', [CarwashApiController::class, 'uploadStaffDocument']);
    Route::delete('/staff/{id}', [CarwashApiController::class, 'deleteStaff']);

    Route::get('/managers', [CarwashApiController::class, 'getManagers']);
    Route::post('/managers', [CarwashApiController::class, 'createManager']);
    Route::put('/managers/{id}', [CarwashApiController::class, 'updateManager']);
    Route::delete('/managers/{id}', [CarwashApiController::class, 'deleteManager']);

    Route::get('/inventory', [CarwashApiController::class, 'getInventory']);
    Route::post('/inventory', [CarwashApiController::class, 'createInventory']);
    Route::put('/inventory/{id}', [CarwashApiController::class, 'updateInventory']);
    Route::delete('/inventory/{id}', [CarwashApiController::class, 'deleteInventory']);

    Route::get('/bays', [CarwashApiController::class, 'getBays']);
    Route::post('/bays', [CarwashApiController::class, 'createBay']);
    Route::delete('/bays/{id}', [CarwashApiController::class, 'deleteBay']);

    Route::get('/jobcards', [CarwashApiController::class, 'getJobCards']);
    Route::post('/jobcards', [CarwashApiController::class, 'createJobCard']);
    Route::put('/jobcards/{id}', [CarwashApiController::class, 'updateJobCard']);
    Route::post('/jobcards/{id}/photos', [CarwashApiController::class, 'uploadJobCardPhoto']);

    Route::get('/invoices', [CarwashApiController::class, 'getInvoices']);
    Route::post('/invoices', [CarwashApiController::class, 'createInvoice']);
    Route::put('/invoices/{id}', [CarwashApiController::class, 'updateInvoice']);
    Route::delete('/invoices/{id}', [CarwashApiController::class, 'deleteInvoice']);

    Route::get('/expenses', [CarwashApiController::class, 'getExpenses']);
    Route::post('/expenses', [CarwashApiController::class, 'createExpense']);
    Route::put('/expenses/{id}', [CarwashApiController::class, 'updateExpense']);
    Route::delete('/expenses/{id}', [CarwashApiController::class, 'deleteExpense']);
    
    Route::get('/settings', [CarwashApiController::class, 'getSettings']);
    Route::post('/settings', [CarwashApiController::class, 'saveSettings']);
});

Route::get('/invoice/{id}/view', function ($id) {
    $invoice = \App\Models\Invoice::find($id);
    if (!$invoice) return "Invoice not found";
    
    $settings = \App\Models\CompanySetting::where('user_id', $invoice->user_id)->first();
    if (!$settings) {
        $settings = new \App\Models\CompanySetting([
            'company_name' => 'Phoenix Autospa',
            'company_tagline' => 'Cleaner Cars Happier Journeys',
            'company_phone' => '9778447160, 8593053592',
            'company_address' => "Kurishumukku Junction, Anchal
Kollam, Kerala - 691306",
            'bank_account_name' => 'Nandu Krishnan',
            'bank_name' => 'Canara Bank',
            'bank_account_number' => '2856101013277',
            'bank_ifsc' => 'CNRB0002856'
        ]);
    }
    
    $subtotal = $invoice->subtotal ?: $invoice->amount;
    if ($invoice->subtotal == 0 && $invoice->amount > 0 && !$invoice->tax_amount && !$invoice->discount_amount) {
        $subtotal = $invoice->amount; 
    }
    $item_price = $invoice->item_price ?? 0;
    $taxRate = $invoice->tax_rate ?? 18;
    $taxAmount = $invoice->tax_amount ?? ($subtotal * ($taxRate / 100));
    $discount = $invoice->discount_amount ?? 0;
    $total = $subtotal + $item_price + $taxAmount - $discount;
    
    $date = $invoice->date ?: $invoice->created_at->format('d M Y');

    return view('invoice-print', compact('invoice', 'settings', 'subtotal', 'item_price', 'taxAmount', 'discount', 'total', 'date'));
});
