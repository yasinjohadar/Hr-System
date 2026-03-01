@extends('admin.layouts.master')

@section('page-title')
    تقرير التدريب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير التدريب</h5>
                </div>
                <div>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للتقارير
                    </a>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports.training') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="employee_id" class="form-select">
                                <option value="">كل الموظفين</option>
                                @foreach ($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="training_id" class="form-select">
                                <option value="">كل الدورات</option>
                                @foreach ($trainings as $training)
                                    <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                        {{ $training->title_ar ?? $training->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="attending" {{ request('status') == 'attending' ? 'selected' : '' }}>يحضر</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الإحصائيات -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي السجلات</h6>
                            <h2 class="mb-0">{{ $stats['total_records'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">مكتملة</h6>
                            <h2 class="mb-0">{{ $stats['completed'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">قيد التنفيذ</h6>
                            <h2 class="mb-0">{{ $stats['in_progress'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">متوسط النتيجة</h6>
                            <h2 class="mb-0">{{ number_format($stats['average_score'], 2) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول التدريب -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجلات التدريب ({{ $records->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>الدورة التدريبية</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>تاريخ الإتمام</th>
                                    <th>النتيجة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $record->employee->full_name }}</strong></td>
                                        <td>{{ $record->training->title_ar ?? $record->training->title }}</td>
                                        <td>{{ $record->registration_date ? $record->registration_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $record->completion_date ? $record->completion_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if ($record->score)
                                                <span class="badge bg-{{ $record->score >= 80 ? 'success' : ($record->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ number_format($record->score, 2) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $record->status == 'completed' ? 'success' : ($record->status == 'attending' ? 'primary' : 'secondary') }}">
                                                {{ $record->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


