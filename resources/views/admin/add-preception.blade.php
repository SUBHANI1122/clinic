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
        <div class="container mt-4">
            <form id="addAppointmentDetailsForm">
                <h5 class="mb-4">Add Medicine Details to Appointment of <strong>{{$ticket->patient->name}}</strong></h5>
                <h6 class="text-primary mt-3">Clinic Notes</h6>
                <hr>

                <input type="hidden" name="appointment_id" id="appointment_id" value="{{$ticket->id}}">
                <div class="skin">
                    <div class="row">
                        <!-- Presenting Complaint Section -->
                        <div class="mb-4 col-md-6">
                            <label for="pc" class="col-form-label">P/C</label>
                            <input type="text" name="pc" id="pc" class="form-control" placeholder="Presenting Complaint">
                        </div>

                        <!-- Procedure Name Section -->
                        <div class="mb-4 col-md-6">
                            <label class="col-form-label">Procedure Name:</label>
                            <p>{{ $ticket->procedure_name }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Next Procedure Date Section -->
                        <div class="mb-4 col-md-6">
                            <label for="next_date" class="col-form-label">Next Procedure Date:</label>
                            <input type="date" name="next_date" id="next_date" class="form-control" placeholder="Next Procedure Date">
                        </div>

                        <!-- Diagnosis Section -->
                        <div class="mb-4 col-md-6">
                            <label for="diagnosis" class="col-form-label">Diagnosis</label>
                            <input type="text" name="diagnosis" id="diagnosis" class="form-control" placeholder="e.g Diagnosis">
                        </div>
                    </div>
                </div>


                <div class="therapy">
                    <div class="mb-4 row">
                        <label class="col-md-2 col-form-label">DM</label>
                        <div class="col-md-4">
                            <input type="checkbox" name="dm" id="dm" class="form-check-input me-2">
                            <label for="dm" class="form-check-label">Diabetes Mellitus</label>
                        </div>
                    </div>
                    <div class="mb-4 row">
                        <label class="col-md-1 col-form-label">B.P</label>
                        <div class="col-md-5">
                            <input type="text" name="bp" class="form-control" placeholder="e.g., 130/90">
                        </div>
                        <label class="col-md-1 col-form-label">P/C</label>
                        <div class="col-md-5">
                            <input type="text" name="pc" class="form-control" placeholder="Presenting Complaint">
                        </div>
                    </div>
                    <div class="mb-4 row">
                        <label class="col-md-1 col-form-label">Diagnosis</label>
                        <div class="col-md-5">
                            <input type="text" name="diagnosis" class="form-control" placeholder="e.g Diagnosis">
                        </div>
                        <label class="col-md-1 col-form-label">Temp.</label>
                        <div class="col-md-5">
                            <input type="text" name="temperature" class="form-control" placeholder="Presenting Temperature">
                        </div>
                    </div>
                </div>
                <h6 class="text-primary mt-4">Medicines</h6>
                <hr>
                <div class="mb-4">
                    <div class="input-group mb-3">
                        <input type="text" id="medicineInput" class="form-control" placeholder="-Select Medicine-">
                        <button type="button" class="btn btn-success" id="addMedicineButton">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                    <ul id="medicineSuggestions" class="list-group mt-2" style="display: none;"></ul>

                    <table class="table mt-3" id="selectedMedicinesTable">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Days</th>
                                <th>Meal Time</th>
                                <th>Doses</th>
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
                    <div class="form-check mb-2">
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

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Details</button>
                </div>
            </form>
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

    <script>
        $(document).ready(function() {

            var doctorName = '{{ $ticket->department }}';

            if (doctorName === 'skin') {
                $('.skin').show();
                $('.therapy').hide();
            } else {
                $('.skin').hide();
                $('.therapy').show();
            }

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
                $('#addMedicineModal').modal('show');
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

            $(document).on('click', '.suggestion', function() {
                const medicineId = $(this).data('id');
                const medicineText = $(this).text();
                addMedicineToTable(medicineId, medicineText);
                $medicineInput.val('').blur();
                $medicineSuggestions.hide();
            });
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
                        const newMedicine = response.medicine;
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: `Medicine is added`,
                        //     text: `Search Added Medicine by name !.`,
                        //     customClass: {
                        //         confirmButton: 'btn btn-success'
                        //     }
                        // });
                        var medicine_name = newMedicine.name + ' - ' + newMedicine.size;
                        addMedicineToTable(newMedicine.id, medicine_name);

                        $('#addMedicineModal').modal('hide');
                        $medicineForm[0].reset();
                    },
                    error: function(xhr) {
                        alert('Error: ' + xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.remove-medicine', function() {
                $(this).closest('tr').remove();
            });
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

            function addMedicineToTable(medicineId, medicineText) {
                if ($selectedMedicinesTable.find(`.medicine-id[value="${medicineId}"]`).length > 0) {
                    alert('This medicine is already added.');
                    return;
                }

                $selectedMedicinesTable.append(`
            <tr>
                <td>${medicineText}</td>
                <td>
                    <input type="number" class="form-control medicine-days" placeholder="Days" min="1" name="medicine_days[]">
                </td>
               <td>
        <select class="form-select" name="meal_timing[${medicineId}]">
            <option value="" selected>- Select Meal Timing -</option>
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
                    <button type="button" class="btn btn-danger remove-medicine"><i class="fa fa-trash"></i></button>
                </td>
                <input type="hidden" class="medicine-id" value="${medicineId}">
            </tr>
            `);
            }
        });
    </script>
    @endsection