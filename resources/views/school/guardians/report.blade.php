@extends('layouts.admin')
@section('title', 'گزارش اولیا')
@section('content')
<div class="container-fluid">
    <h1><i class="fas fa-chart-bar ms-2"></i> گزارش اولیا</h1>
    <div class="row mt-4">
        <div class="col-md-3 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h5>کل اولیا</h5><h2>{{ $totalGuardians }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-info text-white"><div class="card-body"><h5>پدران</h5><h2>{{ $totalFathers }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-success text-white"><div class="card-body"><h5>مادران</h5><h2>{{ $totalMothers }}</h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-secondary text-white"><div class="card-body"><h5>سایر</h5><h2>{{ $totalOthers }}</h2></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3"><div class="card bg-danger text-white"><div class="card-body"><h5>خانواده‌های بدهکار</h5><h2>{{ $debtorFamilies }}</h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-warning text-dark"><div class="card-body"><h5>دارای تخفیف</h5><h2>{{ $discountFamilies }}</h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-dark text-white"><div class="card-body"><h5>دارای یتیم</h5><h2>{{ $orphanFamilies }}</h2></div></div></div>
    </div>
</div>
@endsection
