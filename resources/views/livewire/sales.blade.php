<div class="container">
    <div class="row">
        <div class="col-md-5">
            @include('livewire._search')
        </div>

        <div class="col-md-7">
            <div class="row">
                @include('livewire._cart')
            </div>
            <div class="row mt-3">
                @include('livewire._sales')
            </div>
        </div>

        @include('livewire._return_modal')
        @include('livewire._view_modal')
    </div>
</div>

@include('livewire._scripts')
