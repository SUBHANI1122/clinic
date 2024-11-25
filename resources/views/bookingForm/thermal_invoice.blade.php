<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        /* Add any styles necessary for printing */
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

            .header img {
                max-width: 100px;
                margin-bottom: 10px;
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

            .footer {
                margin-top: 20px;
                text-align: center;
                font-size: 14px;
                color: #888;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="header">
            <!-- <img src="{{ url('images/logo.png') }}" alt="Clinic Logo"> -->
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