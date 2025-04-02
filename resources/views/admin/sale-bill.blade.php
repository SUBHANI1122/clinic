<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                color: #333;
            }

            .invoice-container {
                width: 50%;
                margin: 20px auto;
                padding: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header h2 {
                font-size: 24px;
                color: #333;
                margin: 0;
            }

            .invoice-details p {
                font-size: 16px;
                line-height: 1.5;
                margin: 5px 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <h2>Medical Care & Physiotherapy Clinic</h2>
            <p>Citi Housing B.Block Fountain Chowk Near Sadiq Mart. 03324276305</p>
        </div>

        <h3 style="text-align: center;">Invoice #{{ $sale->id }}</h3>
        <div class="invoice-details">
            <p><strong>Sold by:</strong> {{ $sale->user->name }}</p>
            <p><strong>Date:</strong> {{ $sale->created_at->format('d M Y') }}</p>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px;">Medicine</th>
                    <th style="border: 1px solid black; padding: 8px;">Quantity</th>
                    <th style="border: 1px solid black; padding: 8px;">Sale Price</th>
                    <th style="border: 1px solid black; padding: 8px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $item)
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">{{ $item->medicine->name }} - {{ $item->medicine->size }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $item->quantity }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ number_format($item->sale_price, 2) }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align: right; border: 1px solid black; padding: 8px;">Total:</th>
                    <th style="border: 1px solid black; padding: 8px;">{{ number_format($sale->total_amount, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <div style="text-align: center; margin-top: 20px; font-weight: bold;">
            <p>Thank you for your purchase!</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.location.href = "{{ route('sales') }}";
            };
        };
    </script>
</body>

</html>