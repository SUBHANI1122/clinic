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
        $medicines = Medicine::select(['id', 'name', 'size', 'box_quantity', 'units_per_box', 'price', 'price_per_unit', 'sale_price', 'sale_price_per_unit'])->get();
        return datatables()->of($medicines)->make(true);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'box_quantity' => 'required|integer|min:0',
            'units_per_box' => 'required|integer|min:1',
            'price' => 'required|integer',
        ]);

        $request->merge([
            'price_per_unit' => $request->price / $request->units_per_box
        ]);

        $request->merge([
            'total_units' => $request->box_quantity * $request->units_per_box
        ]);

        $request->merge([
            'sale_price_per_unit' => $request->sale_price / $request->units_per_box
        ]);

        // Create a new medicine record
        $medicine = Medicine::create($request->all());

        return response()->json([
            'success' => true,
            'medicine' => $medicine
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
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
            'total_units' => $request->box_quantity * $request->units_per_box
        ]);

        $request->merge([
            'price_per_unit' => $request->price / $request->units_per_box
        ]);
        $request->merge([
            'sale_price_per_unit' => $request->sale_price / $request->units_per_box
        ]);

        $medicine->update($request->all());

        return response()->json([
            'success' => true,
            'medicine' => $medicine
        ]);
    }


    public function destroy($id)
    {
        Medicine::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
    public function search(Request $request)
    {
        $searchTerm = $request->input('query');

        $medicines = Medicine::where('name', 'LIKE', "%{$searchTerm}%")
            ->get();

        return response()->json($medicines);
    }
}
