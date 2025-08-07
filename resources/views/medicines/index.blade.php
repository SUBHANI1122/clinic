@extends('layouts.app')

@section('content')
<div id="medicineApp"
     data-fetch-url="{{ route('medicines.fetch') }}"
     data-store-url="{{ route('medicines.store') }}"
     data-csrf-token="{{ csrf_token() }}"></div>
<div class="container-fluid">
    <div class="row pb-2">
        <div class="col-md-12 px-0">
            <div class="card border-0 rounded-0">
                <div class="card-header bg-success text-white rounded-0 py-3 d-flex justify-content-between">
                    {{ __('Medicines') }}
                    <button type="button" id="newMedicineBtn" class="btn btn-light btn-sm">
                        Add New Medicine
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center px-5 py-5">
        <div class="table-responsive">
            <table class="table table-bordered" id="medicines-table">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2">#</th>
                        <th scope="col" rowspan="2">Medicine Name</th>
                        <th scope="col" rowspan="2">Box Quantity (Units per Box)</th>
                        <th scope="col" rowspan="2" class="text-center">Total Medicines</th>
                        <th scope="col" rowspan="2" class="text-center">Minimum Quanitity</th>
                        <th scope="col" rowspan="2" class="text-center">Expirey Date</th>
                        <th scope="col" colspan="2" class="text-center">Price (Per Box)</th>
                        <th scope="col" colspan="2" class="text-center">Price (Per Unit)</th>
                        <th scope="col" rowspan="2">Actions</th>
                    </tr>
                    <tr>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Sale Price</th>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Sale Price</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>


</div>

<!-- Medicine Modal -->
<div class="modal fade" id="medicineModal" tabindex="-1" aria-labelledby="medicineModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="medicineModalTitle">Add New Medicine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="medicineName" class="form-label fw-semibold">Medicine Name</label>
                        <input type="hidden" id="medicineId" name="medicine_id">
                        <input type="text" class="form-control" id="medicineName" name="medicine_name" required placeholder="Enter medicine name">
                    </div>
                    <div class="col-md-6">
                        <label for="medicineSize" class="form-label fw-semibold">Size (e.g., 500mg)</label>
                        <input type="text" class="form-control" id="medicineSize" name="medicine_size" required placeholder="Enter size">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="boxQuantity" class="form-label fw-semibold">Box Quantity</label>
                        <input type="number" class="form-control" id="boxQuantity" name="box_quantity" required placeholder="Enter box quantity">
                    </div>
                    <div class="col-md-4">
                        <label for="unitsPerBox" class="form-label fw-semibold">Units per Box</label>
                        <input type="number" class="form-control" id="unitsPerBox" name="units_per_box" required placeholder="Enter units per box">
                    </div>
                    <div class="col-md-4">
                        <label for="unitsPerBox" class="form-label fw-semibold">Total Units</label>
                        <input type="number" class="form-control" id="total_units" name="total_units" required placeholder="Enter units per box">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Purchase Price per Box</label>
                        <input type="text" class="form-control" id="price" name="price" required placeholder="Enter price per box">
                    </div>
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Sale Price per Box</label>
                        <input type="text" class="form-control" id="sale_price" name="sale_price" required placeholder="Enter sale price per box">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Minimum Quantity</label>
                        <input type="number" class="form-control" id="minimum_quantity" name="minimum_quantity" required>
                    </div>
                    <div class="col-md-6">
                        <label for="pricePerBox" class="form-label fw-semibold">Expirey Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="saveMedicine" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/medicines.js') }}"></script>
@endsection
