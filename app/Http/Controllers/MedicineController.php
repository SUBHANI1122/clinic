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
        $medicines = Medicine::select(['id', 'name', 'size'])->get();
        return datatables()->of($medicines)->make(true);
    }

    public function store(Request $request)
    {
        $medicine = Medicine::create($request->all());
        return response()->json([
            'success' => true,
            'medicine' => $medicine
        ]);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->update($request->all());
        return response()->json(['success' => true]);
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
