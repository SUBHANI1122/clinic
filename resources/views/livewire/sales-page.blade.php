<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm p-3 mb-3">
            <h5 class="mb-3">Search Medicine</h5>

            <input type="text" wire:model.debounce.300ms="medicineSearch" class="form-control" placeholder="Search Medicine...">

            <ul class="list-group mt-3">
                @forelse ($medicines as $medicine)
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    wire:click="addMedicine({{ $medicine->id }})" style="cursor: pointer;">
                    <span>{{ $medicine->name }} - {{ $medicine->size }}</span>
                    <div>
                        <span class="badge bg-primary">{{ $medicine->sale_price_per_unit }} Rs Per Unit</span>
                        <span class="badge bg-success">{{ $medicine->total_units }} Available QTY</span>
                    </div>
                </li>
                @empty
                <li class="list-group-item text-center">No medicines found.</li>
                @endforelse
            </ul>
            <div class="mt-2">
                {{ $medicines->links() }}
            </div>
        </div>
    </div>
    <div class="col-md-7">
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
                        <td>{{ $item['name'] }} - {{ $item['size'] }}</td>
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
            <h4 class="text-end mt-3">Total: <strong>{{ number_format($totalAmount, 2) }}</strong></h4>
            <button wire:click="completeSale" class="btn btn-success btn-lg w-100 mt-3">Complete Sale</button>
            @else
            <p class="text-muted text-center">No items in cart</p>
            @endif
            @if(session()->has('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
            @endif
        </div>
    </div>
</div>