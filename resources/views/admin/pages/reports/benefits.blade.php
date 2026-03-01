@extends('admin.layouts.master')

@section('page-title')
    تقرير المزايا
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تقرير المزايا والتعويضات</h5>
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
                    <form method="GET" action="{{ route('admin.reports.benefits') }}" class="row g-3">
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
                            <select name="benefit_type_id" class="form-select">
                                <option value="">كل الأنواع</option>
                                @foreach ($benefitTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('benefit_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ar ?? $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
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
                            <h6 class="card-title">إجمالي المزايا</h6>
                            <h2 class="mb-0">{{ $stats['total_benefits'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title">نشطة</h6>
                            <h2 class="mb-0">{{ $stats['active'] }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6 class="card-title">إجمالي القيمة</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_value'], 2) }} ر.س</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h6 class="card-title">عدد الأنواع</h6>
                            <h2 class="mb-0">{{ $stats['by_type']->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- جدول المزايا -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">مزايا الموظفين ({{ $benefits->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع الميزة</th>
                                    <th>القيمة</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($benefits as $benefit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $benefit->employee->full_name }}</strong></td>
                                        <td>{{ $benefit->benefitType->name_ar ?? $benefit->benefitType->name }}</td>
                                        <td>
                                            @if ($benefit->value)
                                                {{ number_format($benefit->value, 2) }}
                                                @if ($benefit->currency)
                                                    {{ $benefit->currency->symbol_ar ?? $benefit->currency->symbol }}
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $benefit->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $benefit->end_date ? $benefit->end_date->format('Y-m-d') : 'دائم' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $benefit->status == 'active' ? 'success' : ($benefit->status == 'expired' ? 'danger' : 'warning') }}">
                                                {{ $benefit->status_name_ar }}
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


