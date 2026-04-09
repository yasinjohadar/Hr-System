@extends('admin.layouts.master')

@section('page-title')
    تسجيل سلفة
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
                <h5 class="page-title fs-21 mb-3">تسجيل سلفة جديدة</h5>
            </div>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-advances.store') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>
                                            {{ $employee->full_name ?? $employee->first_name . ' ' . $employee->last_name }}
                                            ({{ $employee->employee_code ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">مبلغ السلفة <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" min="0.01" name="principal_amount" class="form-control" value="{{ old('principal_amount') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ المنح</label>
                                <input type="date" name="granted_at" class="form-control" value="{{ old('granted_at', date('Y-m-d')) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">الوصف / الملاحظات</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.employee-advances.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
