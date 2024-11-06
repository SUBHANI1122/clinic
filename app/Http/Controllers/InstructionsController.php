<?php

namespace App\Http\Controllers;

use App\Models\Instructions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructionsController extends Controller
{
    public function index()
    {
        return view('instructions.index');
    }
    public function fetch()
    {
        $labs = Instructions::select(['id', 'instruction', 'added_by', 'created_at'])->with('added_by:name')->get();

        return datatables()->of($labs)->make(true);
    }

    public function store(Request $request)
    {
        $request['added_by'] = Auth::user()->id;
        Instructions::create($request->all());
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {

        $request['added_by'] = Auth::user()->id;
        $medicines = Instructions::findOrFail($id);
        $medicines->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Instructions::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
