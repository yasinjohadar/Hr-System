@extends('admin.layouts.master')

@section('page-title')
    معدل دوران الموظفين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير معدل دوران الموظفين</h5>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">عودة للتقارير</a>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">فلتر الفترة</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.turnover') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">عرض</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">عدد المنتهية خدمتهم</h6>
                            <h2 class="mb-0">{{ $exitsCount }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">متوسط عدد الموظفين (تقريبي)</h6>
                            <h2 class="mb-0">{{ number_format($avgHeadcount, 0) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">معدل الدوران %</h6>
                            <h2 class="mb-0">{{ $turnoverRate }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">الموظفون النشطون حالياً</h6>
                            <h2 class="mb-0">{{ $currentActive }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل إنهاء الخدمة في الفترة</h5>
                </div>
                <div class="card-body">
                    @if($exits->isEmpty())
                        <p class="text-muted mb-0">لا توجد سجلات إنهاء خدمة في الفترة المحددة.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>تاريخ إنهاء الخدمة</th>
                                        <th>النوع</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exits as $exit)
                                        <tr>
                                            <td>{{ $exit->employee->full_name ?? '—' }}</td>
                                            <td>{{ $exit->last_working_day?->format('Y-m-d') ?? $exit->resignation_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $exit->exit_type_name_ar ?? $exit->exit_type }}</td>
                                            <td>{{ $exit->status_name_ar ?? $exit->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
