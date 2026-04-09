@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الراتب
@stop

@php
    $sym = $salary->currency->symbol_ar ?? $salary->currency->symbol ?? 'ر.س';
@endphp

@section('css')
    <style>
        .salary-show-hero {
            background: linear-gradient(145deg, var(--bs-primary) 0%, rgba(var(--bs-primary-rgb), 0.88) 55%, rgb(15, 76, 129) 100%);
            color: #fff;
            border: none;
        }
        .salary-show-hero .text-white-75 { color: rgba(255,255,255,.85) !important; }
        .salary-breakdown-table th {
            font-weight: 600;
            color: var(--bs-secondary-color);
            width: 45%;
            border-bottom-width: 1px;
        }
        .salary-breakdown-table td {
            font-variant-numeric: tabular-nums;
            text-align: end;
        }
        .salary-meta-item {
            padding: 0.65rem 0;
            border-bottom: 1px solid var(--bs-border-color-translucent);
        }
        .salary-meta-item:last-child { border-bottom: 0; }
        .salary-timeline-table th { font-size: 0.8125rem; font-weight: 600; white-space: nowrap; }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل الراتب</h5>
                    <p class="text-muted small mb-0">{{ $salary->month_name }} {{ $salary->salary_year }} — {{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ route('admin.salaries.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right me-1"></i>العودة للقائمة
                    </a>
                    @can('salary-edit')
                        <a href="{{ route('admin.salaries.edit', $salary->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>تعديل
                        </a>
                    @endcan
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card salary-show-hero shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="avatar avatar-md bg-white bg-opacity-25 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:3rem;height:3rem;">
                                    <i class="fas fa-user-tie fs-4"></i>
                                </span>
                                <div class="min-w-0">
                                    <div class="text-white-75 small mb-1">الموظف</div>
                                    <div class="fw-semibold fs-6 text-truncate">{{ $salary->employee->full_name ?? $salary->employee->first_name . ' ' . $salary->employee->last_name }}</div>
                                    <div class="small text-white-75 font-monospace">{{ $salary->employee->employee_code ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom border-white border-opacity-25">
                                <div class="text-white-75 small mb-1"><i class="far fa-calendar-alt me-1"></i>فترة الراتب</div>
                                <div class="fs-5 fw-semibold">{{ $salary->month_name }} {{ $salary->salary_year }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-white-75 small mb-2">حالة الدفع</div>
                                @if ($salary->payment_status == 'paid')
                                    <span class="badge bg-success fs-14 px-3 py-2">مدفوع</span>
                                @elseif ($salary->payment_status == 'pending')
                                    <span class="badge bg-warning text-dark fs-14 px-3 py-2">قيد الانتظار</span>
                                @else
                                    <span class="badge bg-danger fs-14 px-3 py-2">ملغي</span>
                                @endif
                            </div>
                            <div class="mt-auto pt-3 border-top border-white border-opacity-25">
                                <div class="text-white-75 small mb-1">صافي الراتب</div>
                                <div class="display-6 fw-bold lh-1">{{ number_format($salary->total_salary, 2) }}</div>
                                <div class="small text-white-75 mt-1">{{ $sym }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-light py-3 border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-coins text-primary me-2"></i>تفصيل المبالغ وحساب الإجمالي
                            </h6>
                            <small class="text-muted">مجموع الإضافات ناقص الخصومات حتى الوصول للصافي</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table salary-breakdown-table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-money-bill-wave text-muted me-2"></i>الراتب الأساسي
                                            </th>
                                            <td class="pe-4 py-3 align-middle fw-semibold">{{ number_format($salary->base_salary, 2) }} {{ $sym }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-plus-circle text-success me-2"></i>البدلات
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-success fw-semibold">+{{ number_format($salary->allowances, 2) }} {{ $sym }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-gift text-success me-2"></i>المكافآت
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-success fw-semibold">+{{ number_format($salary->bonuses, 2) }} {{ $sym }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="ps-4 py-3 align-middle">
                                                <i class="fas fa-clock text-success me-2"></i>ساعات إضافية
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-success fw-semibold">+{{ number_format($salary->overtime, 2) }} {{ $sym }}</td>
                                        </tr>
                                        <tr class="table-danger bg-danger bg-opacity-10">
                                            <th scope="row" class="ps-4 py-3 align-middle border-danger border-opacity-25">
                                                <i class="fas fa-minus-circle text-danger me-2"></i>الخصومات
                                            </th>
                                            <td class="pe-4 py-3 align-middle text-danger fw-bold">−{{ number_format($salary->deductions, 2) }} {{ $sym }}</td>
                                        </tr>
                                        <tr class="border-top border-2">
                                            <th scope="row" class="ps-4 py-4 align-middle fs-6">
                                                <i class="fas fa-equals text-primary me-2"></i>الراتب الإجمالي (الصافي)
                                            </th>
                                            <td class="pe-4 py-4 align-middle">
                                                <span class="fs-4 fw-bold text-success">{{ number_format($salary->total_salary, 2) }}</span>
                                                <span class="text-muted ms-1">{{ $sym }}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>بيانات الدفع والعملة والسجل
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-0">
                                <div class="col border-bottom border-end-md p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-calendar-check me-1"></i>تاريخ الدفع</div>
                                    <div class="fw-semibold">{{ $salary->payment_date ? $salary->payment_date->format('Y-m-d') : '—' }}</div>
                                </div>
                                <div class="col border-bottom p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-coins me-1"></i>العملة</div>
                                    <div class="fw-semibold">{{ $salary->currency ? ($salary->currency->name_ar ?? $salary->currency->name) . ' (' . $salary->currency->code . ')' : '—' }}</div>
                                </div>
                                <div class="col border-bottom border-end-md border-md-bottom-0 p-3">
                                    <div class="small text-muted mb-1"><i class="far fa-clock me-1"></i>تاريخ الإنشاء</div>
                                    <div class="fw-semibold font-monospace small">{{ $salary->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col border-bottom border-md-bottom-0 p-3">
                                    <div class="small text-muted mb-1"><i class="fas fa-user me-1"></i>أنشأ بواسطة</div>
                                    <div class="fw-semibold">{{ $salary->creator->name ?? '—' }}</div>
                                </div>
                            </div>
                            @if ($salary->notes)
                                <div class="border-top mt-0 px-3 py-3 bg-light bg-opacity-50 rounded-bottom">
                                    <div class="small text-muted mb-1"><i class="fas fa-sticky-note me-1"></i>ملاحظات</div>
                                    <div class="mb-0">{{ $salary->notes }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if ($salary->ledgerLines->isNotEmpty())
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="fas fa-list-ul text-primary me-2"></i>تفاصيل البنود المالية
                                    </h6>
                                    <small class="text-muted">تفكيك الخصومات والبدلات والسلف على مستوى البنود</small>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-4">النوع</th>
                                                <th>الوصف</th>
                                                <th class="text-end">المبلغ</th>
                                                <th class="pe-4">السلفة</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($salary->ledgerLines as $line)
                                                <tr>
                                                    <td class="ps-4"><span class="badge bg-secondary-subtle text-dark border">{{ $line->line_type_name_ar }}</span></td>
                                                    <td>{{ $line->label_ar ?? $line->label ?? '—' }}</td>
                                                    <td class="text-end font-monospace">
                                                        @if ($line->isDeductionSide())
                                                            <span class="text-danger fw-semibold">−{{ number_format($line->amount, 2) }}</span>
                                                        @else
                                                            <span class="text-success fw-semibold">+{{ number_format($line->amount, 2) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="pe-4">
                                                        @if ($line->employeeAdvance)
                                                            <span class="small">سلفة #{{ $line->employee_advance_id }}</span>
                                                            @if ($line->employeeAdvance->status === 'active')
                                                                <span class="badge bg-info">نشطة</span>
                                                            @else
                                                                <span class="badge bg-dark">مغلقة</span>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
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
            @endif

            <div class="row g-3 mt-1 mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fas fa-history text-primary me-2"></i>السجل الزمني والمالي للموظف
                            </h6>
                            <small class="text-muted">رواتب مسجلة وكشوف معالجة؛ قد يظهر الشهر مرتين إن وُجد النوعان</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 salary-timeline-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">المصدر</th>
                                            <th>الفترة</th>
                                            <th class="text-end">المبلغ</th>
                                            <th>الحالة</th>
                                            <th class="pe-4 text-end">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($financialTimeline as $row)
                                            <tr class="@if (!empty($row['is_current'])) table-primary @endif">
                                                <td class="ps-4">
                                                    @if ($row['source'] === 'salary')
                                                        <span class="badge rounded-pill bg-primary">راتب مسجل</span>
                                                    @else
                                                        <span class="badge rounded-pill bg-info text-dark">كشف معالجة</span>
                                                    @endif
                                                </td>
                                                <td class="fw-medium">{{ $row['period_label'] }}</td>
                                                <td class="text-end font-monospace fw-semibold">{{ number_format($row['total'], 2) }}</td>
                                                <td>
                                                    @if ($row['source'] === 'salary')
                                                        @if ($row['status_raw'] === 'paid')
                                                            <span class="badge bg-success">{{ $row['status'] }}</span>
                                                        @elseif ($row['status_raw'] === 'pending')
                                                            <span class="badge bg-warning text-dark">{{ $row['status'] }}</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ $row['status'] }}</span>
                                                        @endif
                                                    @else
                                                        @if ($row['status_raw'] === 'paid')
                                                            <span class="badge bg-success">{{ $row['status'] }}</span>
                                                        @elseif ($row['status_raw'] === 'cancelled')
                                                            <span class="badge bg-danger">{{ $row['status'] }}</span>
                                                        @elseif (in_array($row['status_raw'], ['draft', 'calculated'], true))
                                                            <span class="badge bg-secondary">{{ $row['status'] }}</span>
                                                        @elseif ($row['status_raw'] === 'approved')
                                                            <span class="badge bg-primary">{{ $row['status'] }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $row['status'] }}</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="pe-4 text-end">
                                                    <a href="{{ $row['url'] }}" class="btn btn-sm btn-outline-primary">
                                                        عرض التفاصيل <i class="fas fa-chevron-left ms-1 small"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-5">لا توجد سجلات أخرى</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
