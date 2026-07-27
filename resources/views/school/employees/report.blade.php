@extends('layouts.admin')
@section('title', 'گزارش کارمندان')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">

    </div>
<div class="container-fluid">
    <h1><i class="fas fa-chart-bar ms-2"></i> گزارش کارمندان</h1>
    <div class="row mt-4">
        <div class="col-md-3 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h5>کل کارمندان</h5><h2>{{ $totalEmployees }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-info text-white"><div class="card-body"><h5>مدیران</h5><h2>{{ $managers }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-success text-white"><div class="card-body"><h5>معلمان</h5><h2>{{ $teachers }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-warning text-dark"><div class="card-body"><h5>اداری</h5><h2>{{ $administrative }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-secondary text-white"><div class="card-body"><h5>خدماتی</h5><h2>{{ $service }}</h2></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3"><div class="card bg-success text-white"><div class="card-body"><h5>مجموع معاش ماه جاری</h5><h2>{{ number_format($totalMonthlySalary) }} ؋</h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-warning text-dark"><div class="card-body"><h5>اضافه‌کاری‌ها</h5><h2>{{ number_format($totalOvertime) }} ؋</h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-danger text-white"><div class="card-body"><h5>کسورات</h5><h2>{{ number_format($totalDeductions) }} ؋</h2></div></div></div>
    </div>
</div>
@endsection
