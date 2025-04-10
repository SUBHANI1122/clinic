<div class="card shadow-sm p-3 mb-3">
    <h5 class="mb-3">Search Medicine</h5>
<input id="medicineSearchPage1" type="text" wire:model.debounce.300ms="medicineSearch" class="form-control" placeholder="Search Medicine...">

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
