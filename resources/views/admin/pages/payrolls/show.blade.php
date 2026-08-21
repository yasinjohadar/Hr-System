@extends('admin.layouts.master')

@section('page-title')
    تفاصيل كشف الراتب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-file-paper-2-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تفاصيل كشف الراتب</h1>
                        <p>{{ $payroll->payroll_code }} &mdash; {{ $payroll->employee->full_name }}</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    @if (in_array($payroll->status, ['calculated', 'approved'], true))
                        <a href="{{ route('admin.payrolls.payslip', $payroll->id) }}" class="admin-btn admin-btn-light" target="_blank" rel="noopener">
                            <i class="ri-printer-line"></i>
                            طباعة كشف الراتب
                        </a>
                        <a href="{{ route('admin.payrolls.payslip.pdf', $payroll->id) }}" class="admin-btn admin-btn-danger" target="_blank" rel="noopener">
                            <i class="ri-file-pdf-line"></i>
                            تحميل PDF
                        </a>
                    @endif
                    <a href="{{ route('admin.payrolls.index') }}" class="admin-btn admin-btn-secondary">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">معلومات الموظف</h5>
                        </div>
                        <div class="admin-form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>الموظف:</strong> {{ $payroll->employee->full_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>كود الموظف:</strong> {{ $payroll->employee->employee_code }}
                                </div>
                                <div class="col-md-3">
                                    <strong>الفترة:</strong> {{ $payroll->month_name }} / {{ $payroll->payroll_year }}
                                </div>
                                <div class="col-md-3">
                                    <strong>الحالة:</strong>
                                    <span class="admin-badge admin-badge-{{ match($payroll->status) {
                                        'draft' => 'secondary',
                                        'calculated' => 'info',
                                        'approved' => 'warning',
                                        'paid' => 'success',
                                        default => 'secondary'
                                    } }}">
                                        {{ $payroll->status_name_ar }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-8">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">تفاصيل الراتب</h5>
                        </div>
                        <div class="admin-form-body">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>البند</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>الراتب الأساسي</strong></td>
                                        <td>{{ number_format($payroll->base_salary, 2) }} {{ $payroll->currency->code ?? '' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إجمالي البدلات</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->total_allowances, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>المكافآت</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->bonuses, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>الساعات الإضافية</strong></td>
                                        <td class="text-success">+ {{ number_format($payroll->overtime_amount, 2) }} ({{ number_format($payroll->overtime_hours, 2) }} ساعة)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>إجمالي الخصومات</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->total_deductions, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>خصم الإجازات</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->leave_deduction, 2) }} ({{ $payroll->leave_days }} يوم)</td>
                                    </tr>
                                    <tr>
                                        <td><strong>خصم التأخير</strong></td>
                                        <td class="text-danger">- {{ number_format($payroll->late_deduction, 2) }} ({{ $payroll->late_days }} يوم)</td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>الراتب الإجمالي</strong></td>
                                        <td><strong>{{ number_format($payroll->gross_salary, 2) }}</strong></td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>الراتب الصافي</strong></td>
                                        <td><strong>{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency->code ?? '' }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($payroll->items->count() > 0)
                    <div class="admin-page-card mt-3">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">بنود الراتب</h5>
                        </div>
                        <div class="admin-form-body">
                            <table class="admin-data-table admin-data-table-sm">
                                <thead>
                                    <tr>
                                        <th>النوع</th>
                                        <th>اسم البند</th>
                                        <th>القيمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payroll->items as $item)
                                        <tr>
                                            <td><span class="admin-badge admin-badge-{{ $item->item_type == 'allowance' ? 'success' : ($item->item_type == 'deduction' ? 'danger' : 'role') }}">{{ $item->item_type_name_ar }}</span></td>
                                            <td>{{ $item->item_name_ar ?? $item->item_name }}</td>
                                            <td>{{ number_format($item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">معلومات الحضور</h5>
                        </div>
                        <div class="admin-form-body">
                            <p><strong>أيام العمل:</strong> {{ $payroll->working_days }}</p>
                            <p><strong>أيام الحضور:</strong> {{ $payroll->present_days }}</p>
                            <p><strong>أيام الغياب:</strong> {{ $payroll->absent_days }}</p>
                            <p><strong>أيام التأخير:</strong> {{ $payroll->late_days }}</p>
                        </div>
                    </div>

                    <div class="admin-page-card mt-3">
                        <div class="card-toolbar">
                            <h5 class="mb-0 fw-bold">الإجراءات</h5>
                        </div>
                        <div class="admin-form-body">
                            {{--
                                الحساب والموافقة عمليتان تغيّران حالة الكشف ولا رجعة فيهما
                                من الواجهة، فتمرّان بمودال التأكيد المركزي (data-confirm).
                                سابقاً كانتا تُنفَّذان بنقرة واحدة بلا أي تأكيد.
                            --}}
                            {{-- نفس آلية الفهرس: admin-post-action.js يبني نموذج POST عند التأكيد --}}
                            @if ($payroll->status === 'draft')
                                <button type="button" class="admin-btn admin-btn-primary w-100 mb-2"
                                        data-post-url="{{ route('admin.payrolls.calculate', $payroll->id) }}"
                                        data-post-confirm="احتساب كشف <strong>{{ $payroll->payroll_code }}</strong> تلقائياً؟"
                                        data-post-title="احتساب الراتب"
                                        data-post-type="info"
                                        data-post-btn="احتساب">
                                    <i class="ri-calculator-line"></i>
                                    حساب الراتب تلقائياً
                                </button>
                            @endif

                            @if ($payroll->status === 'calculated')
                                <button type="button" class="admin-btn admin-btn-primary w-100 mb-2"
                                        data-post-url="{{ route('admin.payrolls.approve', $payroll->id) }}"
                                        data-post-confirm="الموافقة على كشف <strong>{{ $payroll->payroll_code }}</strong> بصافي {{ number_format($payroll->net_salary, 2) }}؟"
                                        data-post-title="الموافقة على الراتب"
                                        data-post-type="warning"
                                        data-post-btn="موافقة">
                                    <i class="ri-shield-check-line"></i>
                                    الموافقة على الراتب
                                </button>
                            @endif

                            @can('payroll-edit')
                                @if ($payroll->status !== 'paid')
                                    <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="admin-btn admin-btn-secondary w-100 mb-2">
                                        <i class="ri-pencil-line"></i>
                                        تعديل
                                    </a>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

