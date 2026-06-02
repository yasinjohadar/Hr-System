@extends('admin.layouts.master')
@section('page-title', 'قواعد اكتساب الإجازات')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between my-4">
        <h5>قواعد اكتساب الإجازات</h5>
        <a href="{{ route('admin.leave-accrual-rules.create') }}" class="btn btn-primary btn-sm">إضافة قاعدة</a>
    </div>
    <div class="card table-responsive">
        <table class="table mb-0">
            <thead><tr><th>نوع الإجازة</th><th>أيام/شهر</th><th>الحد الأقصى</th><th>الدولة</th></tr></thead>
            <tbody>
            @foreach($rules as $rule)
                <tr>
                    <td>{{ $rule->leaveType?->name }}</td>
                    <td>{{ $rule->accrual_days_per_month }}</td>
                    <td>{{ $rule->max_balance ?? '—' }}</td>
                    <td>{{ $rule->country?->name ?? 'الكل' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $rules->links() }}
    </div>
</div>
@endsection
