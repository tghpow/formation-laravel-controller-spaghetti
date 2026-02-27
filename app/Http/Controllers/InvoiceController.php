<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('create', Invoice::class);
        $user = $request->user();
        $validated = $request->validated();

        // Création (transaction)
        $invoice = DB::transaction(function () use ($validated, $user) {
            $subtotal = collect($validated['items'])
                ->sum(fn ($it) => (int) $it['qty'] * (float) $it['unit_price']);

            $taxRate = (float) $validated['tax_rate'];
            $taxAmount = $subtotal * ($taxRate / 100);
            $total = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'title' => $validated['title'],
                'client_name' => $validated['client_name'],
                'client_email' => $validated['client_email'] ?? null,
                'tax_rate' => $taxRate,

                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,

                'created_by' => $user->id,
                'status' => 'draft', // exemple
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'label' => $item['label'],
                    'qty' => (int) $item['qty'],
                    'unit_price' => (float) $item['unit_price'],
                    'line_total' => (int) $item['qty'] * (float) $item['unit_price'],
                ]);
            }

            return $invoice;
        });

        // 4) Génération et enregistrement du PDF (spaghetti volontaire — plus tard : Observer)
        $invoice->load('items');
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);
        Storage::disk('local')->put('invoices/'.$invoice->id.'.pdf', $pdf->output());

        // 5) Réponse JSON + lien PDF
        return response()->json([
            'id' => $invoice->id,
            'redirect_to' => route('invoices.show', $invoice),
            'pdf_url' => route('invoices.pdf', $invoice),
        ], 201);
    }

    public function pdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $path = 'invoices/'.$invoice->id.'.pdf';

        if (Storage::disk('local')->exists($path)) {
            return response()->file(Storage::disk('local')->path($path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="facture-'.$invoice->id.'.pdf"',
            ]);
        }

        $invoice->load('items');
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);

        return $pdf->stream('facture-'.$invoice->id.'.pdf');
    }
}
