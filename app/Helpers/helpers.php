<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Medicine;

if (!function_exists('getAvailableMedicines')) {
    function getAvailableMedicines()
    {
        return Cache::remember('available_medicines', now()->addHours(24), function () {
            return Medicine::where('total_units', '>', 0)
                ->orderBy('name')
                ->get();
        });
    }
}

if (!function_exists('getAllMedicines')) {
    function getAllMedicines()
    {
        return Cache::remember('all_medicines', now()->addHours(24), function () {
            return Medicine::orderBy('name')->get();
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
