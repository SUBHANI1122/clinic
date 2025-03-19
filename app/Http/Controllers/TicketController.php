<?php

namespace App\Http\Controllers;

use App\Mail\InstallmentAuthenticationRequiredMail;
use App\Mail\InstallmentFailed;
use App\Models\Installment;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Notifications\ReceiptNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Stripe\Exception\CardException;
use Stripe\Stripe;
use PDF;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Stripe\PaymentIntent;
use Illuminate\Support\Str;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\OAuth\InvalidRequestException;
use Stripe\PaymentMethod;
use App\Mail\InstallmentSuccess;
use App\Mail\TicketFullyPaid;
use App\Models\Appoinment;
use App\Models\User;
use App\Notifications\InvoiceFailedNotification;
use Illuminate\Support\Facades\App;
use Stripe\SetupIntent;
use Stripe\Subscription;
use Stripe\SubscriptionSchedule;
use Illuminate\Support\Facades\Cache;



class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bookingForm.index');
    }
    public function cloverUnitedIndex()
    {
        $doctors = User::where('type', 'doctor')->get();
        return view('bookingForm.clover-united-form', ['doctors' => $doctors]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function searchPatient(Request $request)
    {
        $phone = $request->get('phone');
        $patient = User::where('type', 'patient')
            ->where(function ($query) use ($phone) {
                $query->where('phone', $phone)
                    ->orWhere('name', $phone);
            })->first();
        if ($patient) {
            return response()->json([
                'success' => true,
                'patient' => [
                    'name' => $patient->name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'address' => $patient->address,
                    'age' => $patient->age,
                ]
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Patient not found.']);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'doctor_id' => 'required',
            'department_id' => 'required',
            'age' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('errors', $validator->errors());
        }

        $patient = User::where('phone', $request->phone)
            ->where('name', 'like', '%' . $request->name . '%')
            ->first();
        if (!$patient) {
            $patient = User::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'type' => 'patient',
                'age' => $request->age,
            ]);
        }
        $procedure_amount = $request->procedure_amount ?? 0;

        $total_amount =  $procedure_amount + $request->amount;

        // Create a new appointment record
        $appoinment = Appoinment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'procedure_amount' =>  $procedure_amount,
            'procedure_name' => $request->procedure_name,
            'amount' => $request->amount,
            'total_amount' => $total_amount,
            'appointment_date' => now(),
            'department' => $request->department_id

        ]);

        $doctor = User::findOrFail($request->doctor_id);
        $today = Carbon::today()->format('Y-m-d');
        $cacheKey = "daily_invoice_count_$today";

        $invoiceNumber = Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $invoiceNumber, Carbon::now()->endOfDay());
        // Prepare invoice data
        $invoiceData = [
            'invoice_number' =>$invoiceNumber,
            'appoinment_id' => $appoinment->id,
            'patient_name' => $patient->name,
            'doctor_name' => User::find($request->doctor_id)->name,
            'appointment_date' => now()->toDateString(),
            'total_amount' => $total_amount,
            'amount' => $request->amount,
            'procedure_amount' =>  $procedure_amount,
            'procedure_name' => $request->procedure_name,
            'age' => $request->age,
            'department' => $request->department_id,
        ];

        // Pass data to a view for the invoice
        return view('bookingForm.thermal_invoice', compact('invoiceData'));
    }

    public function sendEmail()
    {
        if (env('APP_ENV') == 'production') {

            $data = [
                'email' => 'saqib.umair@moebotech.com',
                'title' => 'Dummy Email with PDF Attachment',
                'content' => 'This is a test email with dummy data and a PDF attachment.'
            ];

            // Generate PDF (example using a simple view)
            $pdf = PDF::loadView('dummy', $data);

            // Send email with PDF attachment
            Mail::send('dummy', $data, function ($message) use ($data, $pdf) {
                $message->to([$data['email'], 'abdul.haseeb@moebotech.com'])
                    ->subject($data['title'])
                    ->attachData($pdf->output(), 'tickets.pdf');
            });
            return redirect()->route('bookingForm');
        }
        return redirect()->route('bookingForm');
    }
}
