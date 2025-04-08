<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        @media print {
            @page {
                margin: 0; /* Removes any default margin from the page */
            }

            body {
                font-family: monospace;
                font-size: 12px;
                width: 80mm;
                margin: 0;
                padding: 0;
                color: #000;
            }

            .invoice-container {
                padding: 5px;
                margin: 0;
            }

            .header, .footer {
                text-align: center;
                margin-bottom: 10px;
            }

            .header h2 {
                font-size: 16px;
                margin: 0;
            }

            .header p {
                margin: 2px 0;
                font-size: 10px;
            }

            .invoice-details p {
                margin: 2px 0;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            th, td {
                padding: 4px;
                text-align: left;
                font-size: 12px;
                border-bottom: 1px dashed #000;
            }

            tfoot th {
                border-top: 1px solid #000;
            }

            .thank-you {
                text-align: center;
                margin-top: 10px;
                font-size: 12px;
                font-weight: bold;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <h2>Medical Care & Physio</h2>
            <p>Citi Housing B.Block</p>
            <p>Fountain Chowk, Sadiq Mart</p>
            <p>0332-4276305</p>
        </div>

        <div class="invoice-details">
            <p><strong>Invoice #:</strong> {{ $sale->id }}</p>
            <p><strong>Date:</strong> {{ $sale->created_at->format('d M Y') }}</p>
            <p><strong>Sold by:</strong> {{ $sale->user->name }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Sub</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                    <tr>
                        <td>{{ $item->medicine->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->sale_price, 2) }}</td>
                        <td>{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th>{{ number_format($sale->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="thank-you">
            <p>Thank you for your purchase!</p>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();
            window.onafterprint = function () {
                window.location.href = "{{ route('sales') }}";
            };
        };
    </script>
</body>
</html>
