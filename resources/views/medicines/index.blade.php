@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row pb-2">
        <div class="col-md-12 px-0">
            <div class="card border-0 rounded-0">
                <div class="card-header bg-success text-white rounded-0 py-3 d-flex justify-content-between">
                    {{ __('Medicines') }}
                    <button type="button" id="newMedicineBtn" class="btn btn-light btn-sm">
                        Add New Medicine
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center px-5 py-5">
        <div class="table-responsive">
            <table class="table table-bordered" id="medicines-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Medicine Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTable will populate this section -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for adding/editing medicines -->
<div class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="medicineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="medicineModalTitle">Add New Medicine</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="medicineForm">
                    <input type="hidden" id="medicineId" name="medicine_id">

                    <!-- Medicine Name and Size Fields -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="medicineName" class="form-label fw-semibold">Medicine Name</label>
                            <input type="text" class="form-control" id="medicineName" name="medicine_name" required placeholder="Enter medicine name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="medicineSize" class="form-label fw-semibold">Size (e.g., 500mg)</label>
                            <input type="text" class="form-control" id="medicineSize" name="medicine_size" required placeholder="Enter size" required>
                        </div>
                    </div>
                    <!-- Save Button -->
                    <div class="d-flex justify-content-center">
                        <button type="button" id="saveMedicine" class="btn btn-primary btn-lg px-5">Save Medicine</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#medicines-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('medicines.fetch') }}",
            columns: [{
                    data: 'id',
                    name: 'id',
                    searchable: true
                },
                {
                    data: null, 
                    name: 'name',
                    render: function(data, type, row) {
                        return `${row.name} (${row.size})`;
                    },
                    searchable: true 
                },
                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                    <button class="btn btn-info btn-sm editMedicine" data-id="${data}" data-name="${row.name}" data-size="${row.size}">Edit</button>
                    <button class="btn btn-danger btn-sm deleteMedicine" data-id="${data}">Delete</button>
                `;
                    },
                    orderable: false,
                    searchable: false
                }
            ]
        });

        $('#newMedicineBtn').on('click', function() {
            $('#medicineModal').modal('show');
            $('#medicineModalTitle').text('Add New Medicine');
            $('#medicineForm')[0].reset();
            $('#medicineId').val('');
        });

        // Edit medicine button click
        $(document).on('click', '.editMedicine', function() {
            $('#medicineModal').modal('show');
            $('#medicineModalTitle').text('Edit Medicine');
            $('#medicineId').val($(this).data('id'));
            $('#medicineName').val($(this).data('name'));
            $('#medicineSize').val($(this).data('size'));
        });

        // Save or update medicine via AJAX
        $('#saveMedicine').on('click', function() {
            const formData = {
                id: $('#medicineId').val(),
                name: $('#medicineName').val(),
                size: $('#medicineSize').val(),
                _token: "{{ csrf_token() }}"
            };

            const url = formData.id ? `/medicines/${formData.id}` : "{{ route('medicines.store') }}";
            const method = formData.id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#medicineModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: `Medicine`,
                            text: `Medicine Added Succefully !.`,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        table.ajax.reload();
                        $('#medicineForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: `Medicine`,
                            text: `Failed to save medicine !.`,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: `Medicine`,
                        text: `An error occurred. Please try again..`,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    });
                }
            });
        });

        // Delete medicine
        $(document).on('click', '.deleteMedicine', function() {
            const id = $(this).data('id');

            if (confirm('Are you sure you want to delete this medicine?')) {
                $.ajax({
                    url: `/medicines/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: `Medicine`,
                                text: `Medicine Deleted.`,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `Medicine`,
                                text: `An error occurred. Please try again..`,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: `Medicine`,
                            text: `An error occurred. Please try again..`,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection