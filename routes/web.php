<?php

use App\Http\Controllers\InvoiceController;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/simulate-login', function () {
    $user = User::first();
    if (! $user) {
        abort(500, 'Aucun utilisateur en base. Lancez : php artisan db:seed');
    }
    Auth::login($user);

    return redirect('/');
})->name('simulate-login');

Route::post('/invoices', [InvoiceController::class, 'store']);

Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

Route::get('/invoices/{invoice}', function (Invoice $invoice) {
    return response()->json(
        $invoice->load('items'),
    );
})->name('invoices.show');
