    <div class="container">
        <div class="row">
            <!-- Latest Sales -->
            <div class="row mt-3">
                <div class="card shadow-sm p-3">
                    <h5 class="mb-3">Total Sale Records</h5>
                    @if (!empty($latestSales))
                    <table id="salesTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Invoice (#No)</th>
                                <th>Total Price</th>
                                <th>Invoice</th>
                                <th>Return / Sale Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestSales as $sale)
                            @if ($sale->total_amount > 0)
                            <tr>
                                <td>{{ $sale->id }}</td>
                                <td>{{ number_format($sale->total_amount, 2) }}</td>
                                <td>
                                    <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-info btn-sm">Print</button>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" wire:click="loadReturnSale({{ $sale->id }})">
                                        Return
                                    </button>
                                    <button wire:click="viewSaleDetails({{ $sale->id }})" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-eye"></i> View
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

            <div wire:ignore.self class="modal fade" id="returnSaleModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title">Return Sale Items</h5>
                        </div>
                        <div class="modal-body">
                            @if($returnItems)
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Sold Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($returnItems as $index => $item)
                                    <tr>
                                        <td>{{ $item['medicine']['name'] }}</td>
                                        <td>
                                            <input type="number" wire:model="returnItems.{{ $index }}.quantity"
                                                min="0" class="form-control" placeholder="Enter quantity">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" wire:click="processSaleReturn">Process Return</button>
                        </div>
                    </div>
                </div>
            </div>
            <div wire:ignore.self class="modal fade" id="viewSaleModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Sale Details</h5>
                        </div>
                        <div class="modal-body">
                            @if($selectedSale)
                            <p><strong>Invoice No:</strong> {{ $selectedSale->id }}</p>
                            <p><strong>Total Amount:</strong> {{ number_format($selectedSale->total_amount, 2) }} Rs</p>

                            <h5>Sold Items</h5>
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($selectedSaleItems as $item)
                                    <tr>
                                        <td>{{ $item->medicine->name }} - {{ $item->medicine->size }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->sale_price_per_unit, 2) }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h5>Returns (If Any)</h5>
                            @if ($saleReturns)
                            <table class="table table-bordered">
                                <thead class="table-danger">
                                    <tr>
                                        <th>Medicine</th>
                                        <th>Returned Qty</th>
                                        <th>Refund Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($saleReturns as $return)
                                    <tr>
                                        <td>{{ $return->medicine->name }} - {{ $return->medicine->size }}</td>
                                        <td>{{ $return->returned_quantity }}</td>
                                        <td>{{ number_format($return->return_amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <p class="text-muted">No returns for this sale.</p>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            var salesTable = $('#salesTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true
            });
            $('#medicineSearch').on('keyup', function(event) {
                event.stopPropagation();
            });
        });

        window.addEventListener('openReturnSaleModal', event => {
            $('#returnSaleModal').modal('show');
        });

        window.addEventListener('closeReturnSaleModal', event => {
            $('#returnSaleModal').modal('hide');
        });

        window.addEventListener('openViewSaleModal', event => {
            $('#viewSaleModal').modal('show');
        });

        window.addEventListener('closeViewSaleModal', event => {
            $('#viewSaleModal').modal('hide');
        });
    </script>