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
                            <a href="{{ route('patientHeistory', ['id' => $ticket->patient->id]) }}"
                                class="text-decoration-none text-success" style="font-size:14px">View Details</a>
                            @if (Auth::user()->type == 'doctor')
                            <a href="{{ route('add.preception', ['id' => $ticket->id]) }}" class="btn btn-primary btn-sm">
                                Add Items
                            </a>

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



</div>

<script>
    $(document).ready(function() {

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