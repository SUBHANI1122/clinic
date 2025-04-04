@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row pb-2">
        <div class="col-md-12 px-0">
            <div class="card border-0 rounded-0">
                <div class="card-header bg-success text-white rounded-0 py-3">
                    {{ __('Dashboard') }}
                    <button type="button" class="btn text-white btn-lg mobile-menu">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row px-lg-4 px-md-1 px-sm-1">
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Total Appointments</h5>
                            <h5>{{ $total_orders }}</h5>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-ticket fa-2x text-success" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Today Appointments</h5>
                            <h5>{{ $total_orders_today }}</h5>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-ticket fa-2x text-success" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (Auth::user()->type == 'admin')
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Total Appointments Income</h5>
                            <h5>{{ number_format($total_amount) }}</h5>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa-regular fa-money-bill-1 fa-2x text-success" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Today Appointments Income</h5>
                            <h5>{{ number_format($total_amount_today) }}</h5>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa-regular fa-money-bill-1 fa-2x text-success" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Total Sales</h5>
                            <h5>{{ $total_sales }}</h5>
                            <h6>{{ number_format($total_sales_amount, 2) }}</h6>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-shopping-cart fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-success">Today Sales</h5>
                            <h5>{{ $total_sales_today }}</h5>
                            <h6>{{ number_format($total_sales_amount_today, 2) }}</h6>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-calendar-check fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-danger">Total Returns</h5>
                            <h5>{{ $total_returns }}</h5>
                            <h6>{{ number_format($total_returns_amount, 2) }}</h6>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-undo fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4 col-lg-4 mb-3">
            <div class="card bg-light h-100">
                <div class="card-body px-2 py-2">
                    <div class="row">
                        <div class="col-8 col-md-8 col-lg-8">
                            <h5 class="text-danger">Today's Returns</h5>
                            <h5>{{ $total_returns_today }}</h5>
                            <h6>{{ number_format($total_returns_amount_today, 2) }}</h6>
                        </div>
                        <div class="col-4 col-md-4 col-lg-4 text-end pt-2">
                            <i class="fa fa-calendar-times fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection