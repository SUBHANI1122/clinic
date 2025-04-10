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
