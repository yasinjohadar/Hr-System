@extends('admin.layouts.master')

@section('page-title')
    تعديل سلفة
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="my-4">
                <h5 class="page-title fs-21 mb-3">تعديل سلفة — {{ $advance->employee->full_name ?? '' }}</h5>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>المبلغ الأصلي:</strong> {{ number_format($advance->principal_amount, 2) }}</div>
                        <div class="col-md-4"><strong>المتبقي:</strong> {{ number_format($advance->remaining_balance, 2) }}</div>
                        <div class="col-md-4"><strong>الموظف:</strong> {{ $advance->employee->employee_code ?? '' }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.employee-advances.update', $advance) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">تاريخ المنح</label>
                                <input type="date" name="granted_at" class="form-control" value="{{ old('granted_at', $advance->granted_at?->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    <option value="active" @selected(old('status', $advance->status) === 'active')>نشطة</option>
                                    <option value="closed" @selected(old('status', $advance->status) === 'closed')>مغلقة</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $advance->description) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.employee-advances.index') }}" class="btn btn-secondary">رجوع</a>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
