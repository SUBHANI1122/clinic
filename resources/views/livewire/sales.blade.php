<div class="container">
    <div class="row">
        <!-- Medicine Search -->
        <div class="col-md-6">
            <div class="card shadow-sm p-3 mb-3">
                <h5 class="mb-3">Search Medicine</h5>
                <input type="text" wire:model="search" class="form-control" placeholder="Search Medicine...">

                <ul class="list-group mt-3">
                    @foreach ($medicines as $medicine)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        wire:click="addMedicine({{ $medicine->id }})"
                        style="cursor: pointer;">
                        <span>{{ $medicine->name }} - {{ $medicine->sale_price_per_unit }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Sale Cart -->
        <div class="col-md-6">
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
                                <td>${{ $item['sale_price_per_unit'] }}</td>
                                <td>
                                    <input type="number" wire:model="cart.{{ $index }}.quantity"
                                        wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                        class="form-control" style="width: 70px;" min="1">
                                </td>
                                <td>${{ number_format($item['subtotal'], 2) }}</td>
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
                                <table class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Total Price</th>
                                            <th>Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($latestSales as $sale)
                                        <tr>
                                            <td>{{ $sale->created_at->format('d M Y h:i A') }}</td>
                                            <td>${{ number_format($sale->total_amount, 2) }}</td>
                                            <td>
                                                <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-info btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Print
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="text-muted text-center">No sales found</p>
                                @endif
                            </div>
                    </div>
            </div>

        </div>
    </div>

</div>