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
                        <th>Sr #</th>
                        <th>Invoice No</th>
                        <th>Dr Name</th>
                        <th>Patient Name</th>
                        <th>Patient Phone</th>
                        <th>Age</th>
                        <th>Address</th>
                        <th>Appointment Date</th>
                        <th>Total Amount</th>
                        <th>Discount Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
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

</div>

<script>
    $(document).ready(function() {

        $(document).ready(function() {
            let today = {{ $today }};

            function loadAppointments() {
                $('#entries-table').DataTable({
                    destroy: true, 
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('tickets.fetch') }}",
                        data: {
                            today: today
                        }
                    },
                    dom: '<"top"lBf>rtip',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: 'Export to Excel',
                        title: 'Appointments',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                        }
                    }],
                    lengthMenu: [
                        [10, 50, 100, -1],
                        [10, 50, 100, "All"]
                    ],
                    pageLength: 10,
                    columnDefs: [{
                        targets: 0,
                        orderable: false
                    }],
                    order: [
                        [7, 'asc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: data => `invoice-${data}`
                        },
                        {
                            data: 'doctor.name',
                            name: 'doctor.name'
                        },
                        {
                            data: 'patient.name',
                            name: 'patient.name'
                        },
                        {
                            data: 'patient.phone',
                            name: 'patient.phone'
                        },
                        {
                            data: 'patient.age',
                            name: 'patient.age'
                        },
                        {
                            data: 'patient.address',
                            name: 'patient.address'
                        },
                        {
                            data: 'appointment_date',
                            name: 'appointment_date'
                        },
                        {
                            data: 'total_amount',
                            name: 'total_amount'
                        },
                        {
                            data: 'discount',
                            name: 'discount'
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            }

            loadAppointments();

            $('#allAppointments').click(function() {
                today = 0;
                loadAppointments();
            });

            // Switch to Today's Appointments
            $('#todayAppointments').click(function() {
                today = 1;
                loadAppointments();
            });
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
                        $('#discountModal').modal('hide');
                        window.location.reload();
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