@extends('admin.layouts.master')

@section('page-title')
    سجلات الدفع
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">سجلات الدفع</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('payroll-payment-create')
                            <a href="{{ route('admin.payroll-payments.create') }}" class="btn btn-primary btn-sm">إضافة سجل دفع جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.payroll-payments.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="status" class="form-select" style="width: 150px;">
                                        <option value="">كل الحالات</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                    </select>

                                    <select name="payment_method" class="form-select" style="width: 150px;">
                                        <option value="">كل الطرق</option>
                                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                        <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                        <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>شيك</option>
                                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>بطاقة</option>
                                    </select>

                                    <select name="payroll_id" class="form-select" style="width: 200px;">
                                        <option value="">كل كشوف الرواتب</option>
                                        @foreach ($payrolls as $payroll)
                                            <option value="{{ $payroll->id }}" {{ request('payroll_id') == $payroll->id ? 'selected' : '' }}>
                                                {{ $payroll->payroll_code }} - {{ $payroll->employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.payroll-payments.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>كود الدفع</th>
                                            <th>الموظف</th>
                                            <th>كشف الراتب</th>
                                            <th>المبلغ</th>
                                            <th>طريقة الدفع</th>
                                            <th>تاريخ الدفع</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($payments as $payment)
                                            <tr>
                                                <td>{{ $payment->payment_code }}</td>
                                                <td>{{ $payment->payroll->employee->full_name }}</td>
                                                <td>{{ $payment->payroll->payroll_code }}</td>
                                                <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency->code ?? '' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $payment->payment_method_name_ar }}</span>
                                                </td>
                                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match($payment->status) {
                                                        'completed' => 'success',
                                                        'processing' => 'warning',
                                                        'pending' => 'info',
                                                        'failed' => 'danger',
                                                        'cancelled' => 'secondary',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $payment->status_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('payroll-payment-show')
                                                        <a href="{{ route('admin.payroll-payments.show', $payment->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('payroll-payment-edit')
                                                        @if($payment->status !== 'completed')
                                                        <a href="{{ route('admin.payroll-payments.edit', $payment->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endif
                                                        @endcan
                                                        @can('payroll-payment-delete')
                                                        @if($payment->status !== 'completed')
                                                        <form action="{{ route('admin.payroll-payments.destroy', $payment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                        @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">لا توجد سجلات دفع</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

