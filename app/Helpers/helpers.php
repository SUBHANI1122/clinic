<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Medicine;
use Carbon\Carbon;


if (!function_exists('getAvailableMedicines')) {
    function getAvailableMedicines()
    {
        return Cache::remember('available_medicines', now()->addHours(24), function () {
            return $medicines = Medicine::select([
                'id',
                'name',
                'size',
                'box_quantity',
                'units_per_box',
                'total_units',
                'minimum_quantity',
                'expiry_date',
                'price',
                'sale_price',
            ])
                ->whereColumn('total_units', '>', 'minimum_quantity')
                ->whereDate('expiry_date', '>=', Carbon::today())
                ->orderBy('name')
                ->get()
                ->map(function ($medicine) {
                    $units = max(1, $medicine->box_quantity * $medicine->units_per_box);
                    $medicine->price_per_unit = $medicine->price / $units;
                    $medicine->sale_price_per_unit = $medicine->sale_price / $units;
                    return $medicine;
                });
        });
    }
}

if (!function_exists('getAllMedicines')) {
    function getAllMedicines()
    {
        return Cache::remember('all_medicines', now()->addHours(24), function () {
            return $medicines = Medicine::select([
                'id',
                'name',
                'size',
                'box_quantity',
                'units_per_box',
                'total_units',
                'minimum_quantity',
                'expiry_date',
                'price',
                'sale_price',
            ])
                ->whereColumn('total_units', '>', 'minimum_quantity')
                ->whereDate('expiry_date', '>=', Carbon::today())
                ->orderBy('name')
                ->get()
                ->map(function ($medicine) {
                    $units = max(1, $medicine->box_quantity * $medicine->units_per_box);
                    $medicine->price_per_unit = $medicine->price / $units;
                    $medicine->sale_price_per_unit = $medicine->sale_price / $units;
                    return $medicine;
                });
        });
    }
}

if (!function_exists('refreshMedicineCache')) {
    function refreshMedicineCache()
    {
        Cache::forget('available_medicines');
        Cache::forget('all_medicines');
        getAvailableMedicines();
        return getAllMedicines();
    }
}
