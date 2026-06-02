@extends('admin.layouts.master')
@section('page-title', 'قاعدة اكتساب جديدة')
@section('content')
<div class="container-fluid">
    <div class="card p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.leave-accrual-rules.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label">نوع الإجازة</label><select name="leave_type_id" class="form-select" required>@foreach($leaveTypes as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">أيام الاكتساب شهرياً</label><input type="number" step="0.01" name="accrual_days_per_month" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">الحد الأقصى للرصيد</label><input type="number" name="max_balance" class="form-control"></div>
            <div class="mb-3"><label class="form-label">الدولة (اختياري)</label><select name="country_id" class="form-select"><option value="">—</option>@foreach($countries as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
            <button class="btn btn-primary">حفظ</button>
        </form>
    </div>
</div>
@endsection
