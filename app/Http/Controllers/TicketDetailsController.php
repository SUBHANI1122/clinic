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
        $tickets = Appoinment::with('patient', 'doctor')
            ->when(Auth::user()->type !== 'admin', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->orderBy('id', 'desc')
            ->get();
        $medicines = Medicine::get();
        $instructions = Instructions::get();
        $labs = Lab::get();

        return view('admin.entries', ['tickets' => $tickets, 'medicines' => $medicines, 'lab_tests' => $labs, 'instructions' => $instructions]);
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
        // Get today's date
        $today = now()->toDateString(); // Format: YYYY-MM-DD

        $appointments = Appoinment::with('patient', 'doctor')
            ->when(Auth::user()->type !== 'admin', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->whereDate('appointment_date', $today)
            ->orderBy('id', 'desc')
            ->get();

        $medicines = Medicine::get();
        $instructions = Instructions::get();
        $labs = Lab::get();

        return view('admin.entries', [
            'tickets' => $appointments,
            'medicines' => $medicines,
            'lab_tests' => $labs,
            'instructions' => $instructions
        ]);
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
