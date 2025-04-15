<div class="card shadow-sm p-3">
    <h5 class="mb-3">Total Sale Records</h5>
    @if (!empty($latestSales))
    <table id="salesTable" class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Invoice (#No)</th>
                <th>Created At</th>
                <th>Total Price</th>
                <th>Medicines</th>
                <th>Invoice</th>
                <th>Return / Sale Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($latestSales as $sale)
            @if ($sale->total_amount > 0)
            <tr>
                <td>{{ $sale->id }}</td>
                <td>{{ $sale->created_at->format('d M Y h:i A') }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
                <td>
                    @foreach ($sale->items as $item)
                    @if ($item->medicine)
                    @if ($item->medicine->deleted_at)
                    <span class="badge bg-secondary mb-1">
                        {{ $item->medicine->name }} [Deleted] (x{{ $item->quantity }})
                    </span><br>
                    @else
                    <span class="badge bg-primary mb-1">
                        {{ $item->medicine->name }} (x{{ $item->quantity }})
                    </span><br>
                    @endif
                    @endif
                    @endforeach
                </td>
                <td>
                    <button wire:click="printInvoice({{ $sale->id }})" class="btn btn-info btn-sm">Print</button>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" wire:click="loadReturnSale({{ $sale->id }})">Return</button>
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