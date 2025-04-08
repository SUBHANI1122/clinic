<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add any styles necessary for printing */
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
            @if($invoiceData['department'] === 'skin')
                <h2>Skin Aesthetic Clinic</h2>
            @else
                <h2>Medical Care & Physiotherapy Clinic</h2>
            @endif
            <p>Citi Housing B.Block Fountain Chowk Near Sadiq Mart.03324276305</p>
        </div>

        <h3 style="text-align: center;">Invoice # {{$invoiceData['invoice_number']}}</h3>

        <div class="invoice-details" style="align-items: center;">
            <p><strong>Patient Name:</strong> {{ $invoiceData['patient_name'] }}</p>
            <p><strong>Doctor Name:</strong> {{ $invoiceData['doctor_name'] }}</p>
            @if(!empty($invoiceData['procedure_name']))
            <p><strong>Procedure Name:</strong> {{ $invoiceData['procedure_name'] }}</p>
            @endif
            <p><strong>Date:</strong> {{ $invoiceData['appointment_date'] }}</p>
            <p><strong>Total Amount:</strong> {{ $invoiceData['total_amount'] }}</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.location.href = "{{ route('customer.registeration') }}"; // Change to your appointment page route
            };
        };
    </script>
</body>
</html>
