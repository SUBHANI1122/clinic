<div class="container">
    <div class="row">
        <!-- Medicine Search -->
        <div class="col-md-5">
            <div class="card shadow-sm p-3 mb-3">
                <h5 class="mb-3">Search Medicine</h5>
                <input type="text" wire:model="search" class="form-control" placeholder="Search Medicine...">
                <ul class="list-group mt-3">
                    @foreach ($medicines as $medicine)
                    <li class="list-group-item d-flex justify-content-between align-items-center" wire:click="addMedicine({{ $medicine->id }})" style="cursor: pointer;">
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
                                <td>{{ $item['name'] }} - {{  $item['size'] }}</td>
                                <td>{{ $item['sale_price_per_unit'] }}</td>
                                <td>
                                    <input type="number" wire:model="cart.{{ $index }}.quantity" wire:change="updateQuantity({{ $index }}, $event.target.value)" class="form-control" style="width: 70px;" min="1">
                                </td>
                                <td>{{ number_format($item['subtotal'], 2) }}</td>
                                <td>
                                    <button wire:click="removeItem({{ $index }})" class="btn btn-danger btn-sm">Remove</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <h4 class="text-end mt-3">Total: <strong>${{ number_format($totalAmount, 2) }}</strong></h4>
                    <button wire:click="completeSale" class="btn btn-success btn-lg w-100 mt-3">Complete Sale</button>
                    @if ($saleId)
                    <button wire:click="downloadInvoice({{ $saleId }})" class="btn btn-info btn-lg w-100 mt-3">Download Invoice PDF</button>
                    @endif
                    @else
                    <p class="text-muted text-center">No items in cart</p>
                    @endif
                    @if(session()->has('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
                </div>
            </div>

            <!-- Latest Sales -->
            <div class="row mt-3">
                <div class="card shadow-sm p-3">
                    <h5 class="mb-3">Latest 5 Sales</h5>
                    @if (!empty($latestSales))
                    <table id="salesTable" class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Invoice (Date)</th>
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
                                    <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-info btn-sm">Print</button>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm" wire:click="loadReturnSale({{ $sale->id }})">
                                        Return
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
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" wire:click="processSaleReturn">Process Return</button>
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
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true
        });
    });
    window.addEventListener('openReturnSaleModal', event => {
        $('#returnSaleModal').modal('show');
    });

    window.addEventListener('closeReturnSaleModal', event => {
        $('#returnSaleModal').modal('hide');
    });
</script>