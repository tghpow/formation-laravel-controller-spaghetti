<?php

namespace App\Observers;

use App\Jobs\GenerateInvoicePdfJob;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Dispatch le job de génération PDF après commit (pour que les lignes existent).
     */
    public function created(Invoice $invoice): void
    {
        DB::afterCommit(function () use ($invoice) {
            GenerateInvoicePdfJob::dispatch($invoice->id);
        });
    }
}
