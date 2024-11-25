<?php

namespace App\Http\Controllers;

use App\Models\Appoinment;
use App\Models\Ticket;
use App\Models\TicketDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $total_orders = Appoinment::with('patient', 'doctor')
            ->when(Auth::user()->type === 'doctor', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->count();

        $total_orders_today = Appoinment::with('patient', 'doctor')
            ->whereDate('appointment_date', Carbon::today())
            ->when(Auth::user()->type === 'doctor', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->count();
        $total_amount = Appoinment::sum('total_amount');
        $total_amount_today = Appoinment::whereDate('appointment_date', Carbon::today())->sum('total_amount');

        return view('home', ['total_orders' => $total_orders, 'total_orders_today'=>$total_orders_today,'total_amount' => $total_amount, 'total_amount_today' => $total_amount_today]);
    }
}
