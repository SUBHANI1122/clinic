<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details</title>
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;

            }

            .container {
                background: rgba(255, 255, 255, 0.9);
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            .header {
                text-align: center;
                color: #004085;
            }

            .sub-header {
                font-weight: bold;
                color: #888;
                margin-bottom: 0px;
            }

            .doctor-info-section {
                display: flex;
                justify-content: space-around;
                align-items: flex-start;
            }

            .doctor-info {
                text-align: center;
            }

            .doctor-info h2 {
                margin-bottom: 3px;
            }

            .doctor-info p {
                margin: 0;
            }

            .doctor-info-first {
                text-align: left;
            }

            .doctor-info-first h2 {
                margin-bottom: 3px;
            }

            .doctor-info-first p {
                margin: 0;
            }

            .logo {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 20%;
                margin: 0 auto;
            }

            .patient-info,
            .clinic-notes,
            .medicines,
            .lab-tests,
            .footer {
                margin: 10px 0;
            }

            .divider {
                border-top: 2px solid #0056b3;
            }

            .footer {
                text-align: center;
                color: #555;
            }

            .patient-info {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0px;
            }

            .patient-info p {
                margin: 0;
                padding-right: 20px;
            }

            .column {
                float: left;
            }

            .col-4 {
                width: 30%;
            }

            .col-8 {
                width: 65%;
                font-weight: bold;
            }

            .text-right {
                text-align: right;
            }

            .row::after {
                content: "";
                clear: both;
                display: table;
            }

            .vertical-line {
                width: 1px;
                background-color: #000;
                height: 100%;
                margin: 0 20px;
            }

            .column.col-8 {
                width: 65%;
                font-weight: bold;
                height: 400px;
                padding: 20px;
                overflow: auto;
                background-image: url('../images/clipart2685078.png');
                background-size: 50%;
                background-position: center;
                background-repeat: no-repeat;
                position: relative;
            }

            .image-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 255, 0.9);
                z-index: 0;
            }

            .medicines,
            .instructions {
                position: relative;
                z-index: 1;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="header">
            @if($appointment->department === 'skin')
            <h1>Skin Aesthetic Clinic</h1>
            @else
            <h1>Medical Care & Physiotherapy Clinic</h1>
            @endif <div class="sub-header"><u>Not Valid For Court</u></div>
        </div>

        <div class="doctor-info-section">
            <div class="doctor-info-first">
                <h2>Dr Ayesha Afraz</h2>
                <p>MBBS, RMP, FCPS-1 C.M.H Hospital</p>
                <p>Medical care & Physiotherapy Clinic <br> 3pm to 11pm</p>
            </div>
            <div class="logo">
                <img src="{{ url('images/logo.png') }}" alt="Clinic Logo" style="height: 250px;">
            </div>
            <div class="doctor-info">
                <h2>Dr Afraz Ahmad</h2>
                <p>Consultant Physiotherapist Rehab.Specialist</p>
                <p>DPT(UOS),MS-PPT(RIU),MPPTA</p>
                <p>City Hospital Commissoner Road.</p>
                <p>Timing 11:00Am To 2pm</p>
                <p>Islam Central Hospital Comissioner Road </p>
                <p>2:00Pm To 5:00Pm</p>
            </div>
        </div>
        <div class="divider"></div>

        <div class="patient-info">
            <p><strong>Info:</strong></p>
            <p><strong>Name:</strong> {{ $appointment->patient->name }}</p>
            <p><strong>Age:</strong> {{ $appointment->patient->age }}</p>
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_date)->translatedFormat('l, jS F Y') }}</p>
        </div>

        <div class="row">
            <div class="column col-4">
                <div class="clinic-notes">
                    <h3>Clinic Notes</h3>
                    @if ($appointment->clinicNotes )
            @if($appointment->department === 'skin')
                    <p><strong>Presenting Complaint:</strong> {{ $appointment->clinicNotes->pc }}</p>
                    <p><strong>Diagnose:</strong> {{ $appointment->clinicNotes->diagnosis }}</p>
                    <p><strong>Procedure Name:</strong> {{ $appointment->procedure_name }}</p>
                    <p><strong>Next Procedure::</strong>{{ $appointment->clinicNotes->next_date }}</p>


                    @else
                    <p><strong>Diabetes Mellitus:</strong> {{ $appointment->clinicNotes->dm ? 'Yes' : 'No' }}</p>
                    <p><strong>BP:</strong> {{ $appointment->clinicNotes->bp }}</p>
                    <p><strong>Presenting Complaint:</strong> {{ $appointment->clinicNotes->pc }}</p>
                    <p><strong>Diagnose:</strong> {{ $appointment->clinicNotes->diagnosis }}</p>
                    <p><strong>Temperature:</strong> {{ $appointment->clinicNotes->temperature }}</p>
                    @endif
                    @else
                    <p>No clinic notes available for this appointment.</p>
                    @endif
                </div>
                <div class="lab-tests">
                    <h3>Investigation</h3>
                    <ul>
                        @foreach($appointment->labTests as $test)
                        <li>{{ $test->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="vertical-line"></div>

            <div class="column col-8 text-right">
                <div class="image-overlay"></div>
                <div class="medicines">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead style="text-align: center;">
                            <tr>
                                <th style="border: 1px solid #000;">#</th>
                                <th style="border: 1px solid #000;">Medicine</th>
                                <th style="border: 1px solid #000;">Dosage</th>
                                <th style="border: 1px solid #000;">Days</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            @foreach($appointment->medicines as $index => $medicine)
                            <tr style="border: 1px solid #000;">
                                <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                                <td style="border: 1px solid #000;">
                                    {{ $medicine->name }} - {{ $medicine->size }}
                                </td>
                                <td style="border: 1px solid #000;">
                                    @if($medicine->pivot->meal_timing == 'before')
                                    کھانے سے پہلے
                                    @elseif($medicine->pivot->meal_timing == 'after')
                                    کھانے کے بعد
                                    @endif
                                    <br>
                                    @if($medicine->pivot->morning) صبح / @endif
                                    @if($medicine->pivot->afternoon) دوپہر / @endif
                                    @if($medicine->pivot->evening) شام @endif
                                </td>
                                <td style="border: 1px solid #000;">{{ $medicine->pivot->days }} days</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="instructions text-right">
                    <!-- <h3>ہدایات</h3> -->
                    <ul style="direction: rtl;" class="list-unstyled">
                        @foreach($appointment->instructions as $index => $prescription)
                        <li>{{ $prescription->instruction ?: 'No instructions provided.' }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="divider"></div>

    <div class="footer">
        <p>B.Block Fountain Chowk Near Sadiq Mart Citi Housing Sialkot. <strong>Ph:0332 4276305</strong></p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.history.back();
            };
        };
    </script>
</body>

</html>