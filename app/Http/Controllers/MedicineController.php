<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use DataTables;

class MedicineController extends Controller
{
    public function index()
    {
        return view('medicines.index');
    }

    public function fetch()
    {
        $medicines = getAllMedicines();
        return datatables()->of($medicines)->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if ($request->price) {
            $request->merge([
                'price_per_unit' => $request->price / $request->units_per_box,
                'total_units' => $request->box_quantity * $request->units_per_box,
                'sale_price_per_unit' => $request->sale_price / $request->units_per_box,
            ]);
        }

        $medicine = Medicine::create($request->all());

        refreshMedicineCache(); // use helper function

        return response()->json([
            'success' => true,
            'medicine' => $medicine
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'box_quantity' => 'required|integer|min:0',
            'units_per_box' => 'required|integer|min:1',
            'price' => 'required|integer',
            'sale_price' => 'required|integer',
        ]);

        $medicine = Medicine::findOrFail($id);

        $request->merge([
            'total_units' => $request->box_quantity * $request->units_per_box,
            'price_per_unit' => $request->price / $request->units_per_box,
            'sale_price_per_unit' => $request->sale_price / $request->units_per_box,
        ]);

        $medicine->update($request->all());

        refreshMedicineCache(); // use helper function

        return response()->json([
            'success' => true,
            'medicine' => $medicine
        ]);
    }

    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();

        refreshMedicineCache(); // use helper function

        return response()->json(['success' => true]);
    }

    public function search(Request $request)
    {
        $searchTerm = strtolower($request->input('query'));
        $cachedMedicines = getAllMedicines();

        $filtered = collect($cachedMedicines)->filter(function ($medicine) use ($searchTerm) {
            return strpos(strtolower($medicine['name']), $searchTerm) !== false;
        })->values();

        return response()->json($filtered);
    }
}
