<?php

namespace App\Http\Controllers;

use App\Models\Appoinment;
use App\Models\Instructions;
use App\Models\Lab;
use App\Models\Medicine;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Notifications\ReceiptNotification;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Http;



class TicketDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TicketDetail  $ticket_details
     * @return \Illuminate\Http\Response
     */
    public function show(TicketDetail $ticket_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TicketDetail  $ticket_details
     * @return \Illuminate\Http\Response
     */
    public function edit(TicketDetail $ticket_details)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TicketDetail  $ticket_details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TicketDetail $ticket_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TicketDetail  $ticket_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketDetail $ticket_details)
    {
        //
    }
    public function ticketEntries()
    {
        return view('admin.entries', ['today' => 0]);
    }

    public function fetchTickets(Request $request)
    {
        $query = Appoinment::with(['patient:id,name,phone,age,address', 'doctor:id,name'])
            ->select('id', 'doctor_id', 'patient_id', 'appointment_date', 'total_amount', 'discount')
            ->when(Auth::user()->type !== 'admin', function ($q) {
                $q->where('doctor_id', Auth::user()->id);
            });

        // Check if we are fetching today's appointments
        if ($request->has('today') && $request->today == 1) {
            $query->whereDate('appointment_date', now()->toDateString());
        }

        return datatables()->eloquent($query)
            ->addIndexColumn()
            ->editColumn('appointment_date', function ($row) {
                return \Carbon\Carbon::parse($row->appointment_date)->format('d-m-y');
            })
            ->addColumn('actions', function ($row) {
                $buttons = '<a href="' . route('patientHeistory', ['id' => $row->patient->id]) . '" class="text-decoration-none text-success" style="font-size:14px">View Details</a>';

                if (Auth::user()->type == 'doctor') {
                    $buttons .= ' <a href="' . route('add.preception', ['id' => $row->id]) . '" class="btn btn-primary btn-sm">Add Items</a>';
                }

                if (Auth::user()->type == 'admin') {
                    $buttons .= ' <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#discountModal" data-id="' . $row->id . '">Add Discount</button>';
                }

                return $buttons;
            })
            ->rawColumns(['actions']) // Ensure buttons are displayed properly
            ->make(true);
    }

    public function addPreception($id)
    {
        $ticket = Appoinment::with('patient', 'doctor', 'medicines', 'labTests', 'clinicNotes', 'instructions')->find($id);
        $medicines = Medicine::get();
        $instructions = Instructions::get();
        $labs = Lab::get();

        return view('admin.add-preception', ['ticket' => $ticket, 'medicines' => $medicines, 'lab_tests' => $labs, 'instructions' => $instructions]);
    }

    public function todayAppoinments()
    {
        return view('admin.entries', ['today' => 1]);
    }

    public function ticketDetail($id)
    {
        $ticket = Appoinment::with('patient', 'doctor', 'medicines', 'labTests', 'clinicNotes', 'instructions')->find($id);
        return view('admin.ticketDetail', ['appointment' => $ticket]);
    }
    public function patientHeistory($patientId)
    {
        $appointments = Appoinment::with('doctor', 'medicines', 'labTests', 'clinicNotes', 'instructions')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('admin.patient_heistory', ['appointments' => $appointments]);
    }
}
