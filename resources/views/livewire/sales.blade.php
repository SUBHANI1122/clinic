<div class="container" >
    <div class="row">
        <!-- Medicine Search -->
        <div class="col-md-5">
            <div class="card shadow-sm p-3 mb-3">
                <h5 class="mb-3">Search Medicine</h5>
                <input type="text" wire:model="search" class="form-control" placeholder="Search Medicine...">

                <ul class="list-group mt-3">
                    @foreach ($medicines as $medicine)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        wire:click="addMedicine({{ $medicine->id }})"
                        style="cursor: pointer;">
                        <span>{{ $medicine->name }} - {{ $medicine->size }}</span>

                        <div>
                            <span class="badge bg-primary">{{ $medicine->sale_price_per_unit }} Rs Per Unit</span>
                            <span class="badge bg-success">{{ $medicine->total_units }} Available QTY</span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Sale Cart -->
        <div class="col-md-7">
            <div class="row">
                <div class="card shadow-sm p-3">
                    <h5 class="mb-3">Sale Cart</h5>

                    @if (!empty($cart))
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Medicine</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $index => $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['sale_price_per_unit'] }}</td>
                                <td>
                                    <input type="number" wire:model="cart.{{ $index }}.quantity"
                                        wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                        class="form-control" style="width: 70px;" min="1">
                                </td>
                                <td>{{ number_format($item['subtotal'], 2) }}</td>
                                <td>
                                    <button wire:click="removeItem({{ $index }})" class="btn btn-danger btn-sm">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <h4 class="text-end mt-3">Total: <strong>${{ number_format($totalAmount, 2) }}</strong></h4>
                    @if(session()->has('error'))
                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                    @endif
                    <button wire:click="completeSale" class="btn btn-success btn-lg w-100 mt-3">
                        Complete Sale {{ $saleCompleted }}
                    </button>

                    @if ($saleId)
                    <button wire:click="downloadInvoice({{ $saleId }})" class="btn btn-info btn-lg w-100 mt-3">
                        Download Invoice PDF
                    </button>
                    @endif

                    @else
                    <p class="text-muted text-center">No items in cart</p>
                    @endif

                    @if(session()->has('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
                </div>
            </div>
            <br>
            <div class="row">
                <div class="row">
                    <div class="card shadow-sm p-3">
                        <h5 class="mb-3">Latest 5 Sales</h5>

                        @if (!empty($latestSales))
                        <table id="salesTable" class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Invoice  (Date)</th>
                                    <th>Total Price</th>
                                    <th>Invoice</th>
                                    <th>Add Return</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestSales as $sale)
                                @if ($sale->total_amount > 0)
                                <tr>
                                    <td>Invoice- {{ $sale->id }} ({{ $sale->created_at->format('d M Y h:i A') }})</td>
                                    <td>{{ number_format($sale->total_amount, 2) }}</td>
                                    <td>
                                        <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-info btn-sm">
                                            <i class="fas fa-file-pdf"></i> Print
                                        </button>
                                    </td>
                                    <td>
                                        <button wire:click="loadSale({{ $sale->id }})" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Add Return
                                        </button>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted text-center">No sales found</p>
                        @endif
                    </div>
                </div>

                <!-- Edit Sale Modal -->
                <div wire:ignore.self class="modal fade" id="editSaleModal" tabindex="-1" aria-labelledby="editSaleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editing Sale #{{ $selectedSale ? $selectedSale->id : '' }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if ($selectedSale)
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Medicine</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($selectedSaleItems as $item)
                                        <tr>
                                            <td>{{ $item->medicine->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->subtotal, 2) }}</td>
                                            <td>
                                                <button wire:click="removeSaleItem({{ $item->id }})" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        $('#salesTable').DataTable({
            "paging": true, // Enable pagination
            "searching": true, // Enable search
            "ordering": true, // Enable sorting
            "info": true, // Show info (e.g., "Showing 1 to 5 of 5 entries")
            "lengthMenu": [
                [5, 10, 25, -1],
                [5, 10, 25, "All"]
            ], 
            "columnDefs": [{
                    "orderable": false,
                    "targets": [2, 3]
                }
            ]
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('openEditSaleModal', event => {
            var myModal = new bootstrap.Modal(document.getElementById('editSaleModal'));
            myModal.show();
        });
    });
</script>