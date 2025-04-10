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
