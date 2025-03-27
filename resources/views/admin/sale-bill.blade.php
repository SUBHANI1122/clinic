<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .doctor-info-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .doctor-info {
            width: 30%;
        }

        .logo {
            width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Sale Invoice</h1>
            <p>Date: {{ $sale->created_at->format('d M Y') }}</p>
            <p>Sold by: {{ $sale->user->name }}</p>
        </div>

        <div class="doctor-info-section">
            <div class="doctor-info">
                <h2>Dr. Ayesha Afraz</h2>
                <p>MBBS, RMP, FCPS-1</p>
                <p>Medical Care & Physiotherapy Clinic</p>
                <p>Timings: 3PM - 11PM</p>
            </div>
            <div>
                <img class="logo" src="{{ url('images/logo.png') }}" alt="Clinic Logo">
            </div>
            <div class="doctor-info">
                <h2>Dr. Afraz Ahmad</h2>
                <p>Consultant Physiotherapist</p>
                <p>City Hospital Commissoner Road</p>
                <p>Timings: 11AM - 5PM</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Sale Price</th>
                    <th>Subtotal</th>
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
            <tfoot>
                <th colspan="3" style="text-align: right;">
                Total:
                </th>
                <th >
                    <h3>{{ number_format($sale->total_amount, 2) }}</h3>
                </th>
            </tfoot>
            </tbody>
        </table>



        <div class="footer">
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