@extends('admin.layouts.master')

@section('page-title')
    فعالية التدريب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير فعالية التدريب</h5>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary btn-sm">عودة للتقارير</a>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">فلتر الفترة</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.training-effectiveness') }}" class="row g-3">
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
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">سجلات مكتملة</h6>
                            <h2 class="mb-0">{{ $stats['completed_records'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">موظفون تم تدريبهم</h6>
                            <h2 class="mb-0">{{ $stats['employees_trained'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">دورات مكتملة (مميزة)</h6>
                            <h2 class="mb-0">{{ $stats['courses_completed'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">معدل الإكمال %</h6>
                            <h2 class="mb-0">{{ $stats['completion_rate'] }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <h6 class="card-title">متوسط الدرجة</h6>
                            <h2 class="mb-0">{{ $stats['average_score'] ?? '—' }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">السجلات المكتملة</h5>
                </div>
                <div class="card-body">
                    @if($completedRecords->isEmpty())
                        <p class="text-muted mb-0">لا توجد سجلات تدريب مكتملة في الفترة المحددة.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>الموظف</th>
                                        <th>الدورة</th>
                                        <th>تاريخ الإكمال</th>
                                        <th>الدرجة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedRecords as $rec)
                                        <tr>
                                            <td>{{ $rec->employee->full_name ?? '—' }}</td>
                                            <td>{{ $rec->training->name ?? '—' }}</td>
                                            <td>{{ $rec->completion_date?->format('Y-m-d') ?? '—' }}</td>
                                            <td>{{ $rec->score ?? '—' }}</td>
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
