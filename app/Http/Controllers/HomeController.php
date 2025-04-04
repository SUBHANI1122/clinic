<?php

namespace App\Http\Controllers;

use App\Models\Appoinment;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Ticket;
use App\Models\TicketDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        // Total Appointments
        $total_orders = Appoinment::with('patient', 'doctor')
            ->when(Auth::user()->type === 'doctor', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->count();

        // Today's Appointments
        $total_orders_today = Appoinment::with('patient', 'doctor')
            ->whereDate('appointment_date', Carbon::today())
            ->when(Auth::user()->type === 'doctor', function ($query) {
                $query->where('doctor_id', Auth::user()->id);
            })
            ->count();

        // Total Income from Appointments
        $total_amount = Appoinment::sum('total_amount');
        $total_amount_today = Appoinment::whereDate('appointment_date', Carbon::today())->sum('total_amount');

        // Total sales count and amount
        $total_sales = Sale::count();
        $total_sales_amount = Sale::sum('total_amount');

        // Today's sales count and amount
        $total_sales_today = Sale::whereDate('created_at', Carbon::today())->count();
        $total_sales_amount_today = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');

        // Total returns count and amount
        $total_returns = SaleReturn::count();
        $total_returns_amount = SaleReturn::sum('return_amount');

        // Today's returns count and amount
        $total_returns_today = SaleReturn::whereDate('created_at', Carbon::today())->count();
        $total_returns_amount_today = SaleReturn::whereDate('created_at', Carbon::today())->sum('return_amount');

        return view('home', [
            'total_orders' => $total_orders,
            'total_orders_today' => $total_orders_today,
            'total_amount' => $total_amount,
            'total_amount_today' => $total_amount_today,
            'total_sales' => $total_sales,
            'total_sales_today' => $total_sales_today,
            'total_sales_amount' => $total_sales_amount,
            'total_sales_amount_today' => $total_sales_amount_today,
            'total_returns' => $total_returns,
            'total_returns_today' => $total_returns_today,
            'total_returns_amount' => $total_returns_amount,
            'total_returns_amount_today' => $total_returns_amount_today,
        ]);
    }
}
