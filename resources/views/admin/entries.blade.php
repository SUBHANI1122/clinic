@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row pb-2">
        <div class="col-md-12 px-0">
            <div class="card border-0 rounded-0">
                <div class="card-header bg-success text-white rounded-0 py-3">
                    {{ __('Appointments') }}
                    <button type="button" class="btn text-white btn-lg mobile-menu">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center px-5 py-5">
        <div class="table-responsive">
            <table class="table table-bordered" id="entries-table">
                <thead>
                    <tr>
                        <th scope="col">Sr #</th>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Dr Name</th>
                        <th scope="col">Patient Name</th>
                        <th scope="col">Patient Phone</th>
                        <th scope="col">Age</th>
                        <th scope="col">Address</th>
                        <th scope="col">Appointment Date</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col">Discount Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $index => $ticket)
                    <tr>
                        <td>{{ $index + 1}}</td>
                        <td>invoice-{{$ticket->id }}</td>
                        <td>{{ $ticket->doctor->name }}</td>
                        <td>{{ $ticket->patient->name }}</td>
                        <td>{{ $ticket->patient->phone }}</td>
                        <td>{{ $ticket->patient->age }}</td>
                        <td>{{ $ticket->patient->address }}</td>
                        <td>{{ \Carbon\Carbon::parse($ticket->appointment_date)->format('d-m-y') }}</td>
                        <td>{{ $ticket->total_amount }}</td>
                        <td>{{ $ticket->discount }}</td>
                        <td>
                            <a href="{{ route('ticketDetail', ['id' => $ticket->id]) }}"
                                class="text-decoration-none text-success" style="font-size:14px">View Details</a>
                            @if (Auth::user()->type == 'doctor')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addAppointmentDetailsModal" data-id="{{ $ticket->id }}" data-doctor="{{$ticket->doctor->name}}">
                                Add Items
                            </button>
                            @endif
                            @if (Auth::user()->type == 'admin')
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#discountModal" data-id="{{ $ticket->id }}">
                                Add Discount
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Discount Modal -->
    <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="discountModalLabel">Add Discount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="discountForm">
                        <div class="mb-3">
                            <label for="discount" class="form-label">Discount Amount</label>
                            <input type="number" class="form-control" id="discount" name="discount" required>
                        </div>
                        <input type="hidden" id="ticketId" name="ticketId">
                        <button type="submit" class="btn btn-primary">Save Discount</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Add Appointment Details Modal -->
    <div class="modal fade" id="addAppointmentDetailsModal" tabindex="-1" aria-labelledby="addAppointmentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="addAppointmentDetailsForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAppointmentDetailsModalLabel">Add Details to Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4"> <!-- Added padding -->
                        <input type="hidden" name="appointment_id" id="appointment_id">
                        <div class="therapy">
                            <!-- Clinic Notes Section -->
                            <h6 class="text-primary mt-3">Clinic Notes</h6>
                            <hr>

                            <div class="mb-4 row"> <!-- Increased spacing for rows -->
                                <label class="col-md-2 col-form-label">DM</label>
                                <div class="col-md-4">
                                    <input type="checkbox" name="dm" id="dm" class="form-check-input me-2">
                                    <label for="dm" class="form-check-label">Diabetes Mellitus</label>
                                </div>
                            </div>
                            <div class="mb-4 row">
                                <label class="col-md-1 col-form-label">BP</label>
                                <div class="col-md-5">
                                    <input type="text" name="bp" class="form-control" placeholder="e.g., 130/90">
                                </div>
                                <label class="col-md-1 col-form-label">PC</label>
                                <div class="col-md-5">
                                    <input type="text" name="pc" class="form-control" placeholder="Presenting Complaint">
                                </div>
                            </div>
                            <div class="mb-4 row">
                                <label class="col-md-1 col-form-label">Diagnosis</label>
                                <div class="col-md-5">
                                    <input type="text" name="diagnosis" class="form-control" placeholder="e.g Diagnosis">
                                </div>
                                <label class="col-md-1 col-form-label">Temprature</label>
                                <div class="col-md-5">
                                    <input type="text" name="temperature" class="form-control" placeholder="Presenting Temprature">
                                </div>
                            </div>
                        </div>

                        <!-- Medicines Section -->
                        <h6 class="text-primary mt-4">Medicines</h6>
                        <hr>
                        <div class="mb-4">
                            <div class="input-group mb-3">
                                <input type="text" id="medicineInput" class="form-control" placeholder="-Select Medicine-">
                                <button type="button" class="btn btn-success" id="addMedicineButton" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <ul id="medicineSuggestions" class="list-group mt-2" style="display: none;"></ul> <!-- Suggestions dropdown -->

                            <table class="table mt-3" id="selectedMedicinesTable">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th>Days</th>
                                        <th>Meal Time</th>
                                        <th>Doases</th>
                                        <th>Remove</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows will be appended here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Lab Tests Section -->
                        <h6 class="text-primary mt-4">Lab Tests</h6>
                        <hr>
                        <div class="mb-4">
                            @foreach($lab_tests as $test)
                            <div class="form-check mb-2"> <!-- Added margin below each check -->
                                <input type="checkbox" class="form-check-input me-2" name="lab_tests[]" value="{{ $test->id }}" id="test_{{ $test->id }}">
                                <label class="form-check-label" for="test_{{ $test->id }}">{{ $test->name }}</label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Prescriptions Section -->
                        <h6 class="text-primary mt-4">Instructions</h6>
                        <hr>
                        <div class="mb-4">
                            @foreach ($instructions as $instruction)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="instruction_ids[]" value="{{ $instruction->id }}" id="instruction_{{ $instruction->id }}">
                                <label class="form-check-label" for="instruction_{{ $instruction->id }}">
                                    {{ $instruction->instruction }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="medicineForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMedicineModalLabel">Add New Medicine</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="medicineName" class="form-label">Medicine Name</label>
                            <input type="text" class="form-control" id="medicineName" name="medicine_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="medicineSize" class="form-label">Medicine Size</label>
                            <input type="text" class="form-control" id="medicineSize" name="medicine_size" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        const $medicineInput = $('#medicineInput');
        const $medicineSuggestions = $('#medicineSuggestions');
        const $selectedMedicinesTable = $('#selectedMedicinesTable tbody');
        const $medicineForm = $('#medicineForm');
        const $addAppointmentDetailsForm = $('#addAppointmentDetailsForm');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#addMedicineButton').on('click', function() {
            $('#addAppointmentDetailsModal').modal('hide');
            $('#addMedicineModal').modal('show');
        });

        $('#addMedicineModal').on('hidden.bs.modal', function() {
            $('#addAppointmentDetailsModal').modal('show');
        });

        $medicineInput.on('keyup', function() {
            const query = $(this).val().trim();

            if (query) {
                $.ajax({
                    url: '{{ route("medicines.search") }}',
                    method: 'GET',
                    data: {
                        query
                    },
                    success: function(data) {
                        $medicineSuggestions.empty();
                        if (data.length > 0) {
                            data.forEach(medicine => {
                                $medicineSuggestions.append(
                                    `<li class="list-group-item suggestion" data-id="${medicine.id}">${medicine.name} - ${medicine.size}</li>`
                                );
                            });
                            $medicineSuggestions.show();
                        } else {
                            $medicineSuggestions.hide();
                        }
                    }
                });
            } else {
                $medicineSuggestions.hide();
            }
        });

        // Add selected medicine to table
        $(document).on('click', '.suggestion', function() {
            const medicineId = $(this).data('id');
            const medicineText = $(this).text();
            addMedicineToTable(medicineId, medicineText);
            $medicineInput.val('').blur(); // Clear input and remove focus
            $medicineSuggestions.hide(); // Hide suggestions
        });

        // Add medicine form submission
        $medicineForm.on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '{{ route("medicines.store") }}',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: $('#medicineName').val(),
                    size: $('#medicineSize').val()
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: `Medicine is added`,
                        text: `Search Added Medicine by name !.`,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                    $('#addMedicineModal').modal('hide');
                    $medicineForm[0].reset();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        // Remove medicine row from table
        $(document).on('click', '.remove-medicine', function() {
            $(this).closest('tr').remove();
        });

        // Set appointment ID in modal
        $('button[data-bs-target="#addAppointmentDetailsModal"]').on('click', function() {
            $('#appointment_id').val($(this).data('id'));
            var doctor = $(this).data('doctor');
            if (doctor === 'Dr Ayesha Afraz') {
                $('.therapy').css('display', 'none'); // Corrected the selector and method
            }
        });

        // Add appointment details form submission
        $addAppointmentDetailsForm.on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            const medicines = [];
            $selectedMedicinesTable.find('tr').each(function() {
                const medicineId = $(this).find('.medicine-id').val();
                const days = $(this).find('.medicine-days').val();
                if (medicineId && days) {
                    medicines.push({
                        id: medicineId,
                        days
                    });
                }
            });

            formData.append('medicines', JSON.stringify(medicines));

            $.ajax({
                type: 'POST',
                url: '/appointments/add-details',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Appointment',
                            text: 'Prescriptions Added Successfully!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        }).then(() => {
                            window.location.href = `/ticketDetail/${response.id}`;
                        });
                        $('#addAppointmentDetailsModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        Object.values(errors).forEach(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Appointment',
                                text: error[0],
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Appointment',
                            text: 'An unexpected error occurred. Please try again.',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                }
            });
        });

        // Utility function to add medicine row to table
        function addMedicineToTable(medicineId, medicineText) {
            // Check if medicine is already in the table to avoid duplicates
            if ($selectedMedicinesTable.find(`.medicine-id[value="${medicineId}"]`).length > 0) {
                alert('This medicine is already added.');
                return; // Exit if duplicate found
            }

            $selectedMedicinesTable.append(`
            <tr>
                <td>${medicineText}</td>
                <td>
                    <input type="number" class="form-control medicine-days" placeholder="Days" min="1" name="medicine_days[]">
                </td>
                <td>
                    <select class="form-select" name="meal_timing[]">
                        <option value="" disabled selected>- Select Meal Timing -</option>
                        <option value="before">Before Meal</option>
                        <option value="after">After Meal</option>
                    </select>
                </td>
                <td>
                    <div>
                        <input type="checkbox" id="morning_${medicineId}" name="time_slots[${medicineId}][]" value="morning" class="form-check-input">
                        <label for="morning_${medicineId}" class="form-check-label">Morning</label>
                    </div>
                    <div>
                        <input type="checkbox" id="afternoon_${medicineId}" name="time_slots[${medicineId}][]" value="afternoon" class="form-check-input">
                        <label for="afternoon_${medicineId}" class="form-check-label">Afternoon</label>
                    </div>
                    <div>
                        <input type="checkbox" id="evening_${medicineId}" name="time_slots[${medicineId}][]" value="evening" class="form-check-input">
                        <label for="evening_${medicineId}" class="form-check-label">Evening</label>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove-medicine">Remove</button>
                </td>
                <input type="hidden" class="medicine-id" value="${medicineId}">
            </tr>
            `);
        }

        // Reset modal when hidden
        $('#addMedicineModal').on('hidden.bs.modal', function() {
            $medicineForm[0].reset();
            $medicineSuggestions.hide();
            $selectedMedicinesTable.empty();
        });

        $('#discountModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var ticketId = button.data('id');
            $('#ticketId').val(ticketId);
        });

        // Handle form submission
        $('#discountForm').submit(function(event) {
            event.preventDefault();

            var ticketId = $('#ticketId').val();
            var discountAmount = $('#discount').val();

            $.ajax({
                url: '{{ route("addDiscount") }}', 
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ticket_id: ticketId,
                    discount_amount: discountAmount
                },
                success: function(response) {
                    if (response.success) {
                        $('tr').each(function() {
                            if ($(this).find('td').eq(1).text().trim() == "invoice-" + ticketId) {
                                $(this).find('td').eq(7).text('Discount: ' + discountAmount);
                            }
                        });
                        Swal.fire({
                            icon: 'success',
                            title: 'Discount Added',
                            text: 'Discount has been successfully added to the invoice.',
                        });

                        // Close the modal
                        $('#discountModal').modal('hide');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.',
                        });
                    }
                }
            });
        });
    });
</script>


@endsection