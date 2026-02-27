<?php

namespace App\Jobs;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $invoiceId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoice = Invoice::with('items')->findOrFail($this->invoiceId);

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);
        Storage::disk('local')->put('invoices/'.$invoice->id.'.pdf', $pdf->output());
    }
}
