<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 24px; }
        .meta { margin-bottom: 24px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 24px; width: 280px; margin-left: auto; }
        .totals tr { border: none; }
        .totals td { border: none; padding: 4px 0; }
        .totals .total-line { border-top: 2px solid #333; font-weight: bold; padding-top: 8px; margin-top: 8px; }
        .mt-4 { margin-top: 24px; }
    </style>
</head>
<body>
    <h1>{{ $invoice->title }}</h1>

    <div class="meta">
        <strong>Client :</strong> {{ $invoice->client_name }}<br>
        @if($invoice->client_email)
            <strong>Email :</strong> {{ $invoice->client_email }}<br>
        @endif
        <strong>Date :</strong> {{ $invoice->created_at->format('d/m/Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Qté</th>
                <th class="text-right">P.U. HT (€)</th>
                <th class="text-right">Total HT (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->label }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', ' ') }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Total HT</td>
            <td class="text-right">{{ number_format($invoice->subtotal, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td>TVA ({{ number_format($invoice->tax_rate, 1, ',', '') }} %)</td>
            <td class="text-right">{{ number_format($invoice->tax_amount, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td class="total-line">Total TTC</td>
            <td class="text-right total-line">{{ number_format($invoice->total, 2, ',', ' ') }} €</td>
        </tr>
    </table>
</body>
</html>
