<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Medicine;

if (!function_exists('getCachedMedicines')) {
    function getCachedMedicines()
    {
        return Cache::remember('all_medicines', now()->addHours(24), function () {
            return Medicine::all();
        });
    }
}

if (!function_exists('refreshMedicineCache')) {
    function refreshMedicineCache()
    {
        Cache::forget('all_medicines');
        return getCachedMedicines();
    }
}
