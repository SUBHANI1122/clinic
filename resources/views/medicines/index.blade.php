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
                        <th scope="col" rowspan="2">#</th>
                        <th scope="col" rowspan="2">Medicine Name</th>
                        <th scope="col" rowspan="2">Size</th>
                        <th scope="col" rowspan="2">Box Quantity (Units per Box)</th>
                        <th scope="col" colspan="2" class="text-center">Price (Per Box)</th>
                        <th scope="col" colspan="2" class="text-center">Price (Per Unit)</th>
                        <th scope="col" rowspan="2">Actions</th>
                    </tr>
                    <tr>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Sale Price</th>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Sale Price</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>


</div>

<!-- Medicine Modal -->
<div class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="medicineModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="medicineModalTitle">Add New Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="medicineName" class="form-label fw-semibold">Medicine Name</label>
                        <input type="hidden" id="medicineId" name="medicine_id">
                        <input type="text" class="form-control" id="medicineName" name="medicine_name" required placeholder="Enter medicine name">
                    </div>
                    <div class="col-md-6">
                        <label for="medicineSize" class="form-label fw-semibold">Size (e.g., 500mg)</label>
                        <input type="text" class="form-control" id="medicineSize" name="medicine_size" required placeholder="Enter size">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="boxQuantity" class="form-label fw-semibold">Box Quantity</label>
                        <input type="number" class="form-control" id="boxQuantity" name="box_quantity" required placeholder="Enter box quantity">
                    </div>
                    <div class="col-md-6">
                        <label for="unitsPerBox" class="form-label fw-semibold">Units per Box</label>
                        <input type="number" class="form-control" id="unitsPerBox" name="units_per_box" required placeholder="Enter units per box">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Purchase Price per Box</label>
                        <input type="text" class="form-control" id="price" name="price" required placeholder="Enter price per box">
                    </div>
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Sale Price per Box</label>
                        <input type="text" class="form-control" id="sale_price" name="sale_price" required placeholder="Enter sale price per box">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveMedicine" class="btn btn-primary">Save</button>
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
                    data: 'name',
                    name: 'name',
                    searchable: true
                },
                {
                    data: 'size',
                    name: 'size',
                    searchable: true
                },
                {
                    data: null,
                    name: 'box_quantity',
                    searchable: true,
                    render: function(data, type, row) {
                        return `${row.box_quantity} (${row.units_per_box})`;
                    }
                },

                {
                    data: 'price',
                    name: 'price',
                    searchable: true
                },
                {
                    data: 'sale_price',
                    name: 'sale_price',
                    searchable: true
                },

                {
                    data: 'price_per_unit',
                    name: 'price_per_unit',
                    searchable: true
                },
                {
                    data: 'sale_price_per_unit',
                    name: 'sale_price_per_unit',
                    searchable: true
                },

                {
                    data: 'id',
                    render: function(data, type, row) {
                        return `
                    <button class="btn btn-info btn-sm editMedicine" 
                        data-id="${data}" 
                        data-name="${row.name}" 
                        data-size="${row.size}" 
                        data-box_quantity="${row.box_quantity}" 
                        data-units_per_box="${row.units_per_box}"
                        data-price="${row.purchase_price_per_box}" 
                        data-sale_price="${row.sale_price}">
                        Edit
                    </button>
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

        $(document).on('click', '.editMedicine', function() {
            $('#medicineModal').modal('show');
            $('#medicineModalTitle').text('Edit Medicine');
            $('#medicineId').val($(this).data('id'));
            $('#medicineName').val($(this).data('name'));
            $('#medicineSize').val($(this).data('size'));
            $('#boxQuantity').val($(this).data('box_quantity'));
            $('#unitsPerBox').val($(this).data('units_per_box'));
            $('#price').val($(this).data('price'));
            $('#sale_price').val($(this).data('sale_price'));
        });

        // Save or update medicine via AJAX
        $('#saveMedicine').on('click', function() {
            const formData = {
                id: $('#medicineId').val(),
                name: $('#medicineName').val(),
                size: $('#medicineSize').val(),
                box_quantity: $('#boxQuantity').val(),
                units_per_box: $('#unitsPerBox').val(),
                price: $('#price').val(),
                sale_price: $('#sale_price').val(),
                _token: "{{ csrf_token() }}"
            };

            const url = formData.id ? `/medicines/${formData.id}` : "{{ route('medicines.store') }}";
            const method = formData.id ? 'PATCH' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#medicineModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Medicine',
                            text: 'Medicine saved successfully!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                        table.ajax.reload();
                        $('#medicineForm')[0].reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Medicine',
                            text: 'Failed to save medicine!',
                            customClass: {
                                confirmButton: 'btn btn-success'
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Medicine',
                        text: 'An error occurred. Please try again.',
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