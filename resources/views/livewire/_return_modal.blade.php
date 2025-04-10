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
                                <input type="number" wire:model="returnItems.{{ $index }}.quantity" min="0" class="form-control" placeholder="Enter quantity">
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
