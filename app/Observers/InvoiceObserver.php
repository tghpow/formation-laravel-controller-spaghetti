<?php

namespace App\Observers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceObserver
{
    /**
     * Génère et enregistre le PDF après création de la facture (après commit, pour avoir les lignes).
     */
    public function created(Invoice $invoice): void
    {
        DB::afterCommit(function () use ($invoice) {
            $invoice->load('items');
            $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);
            Storage::disk('local')->put('invoices/'.$invoice->id.'.pdf', $pdf->output());
        });
    }
}
