@extends('admin.layouts.master')

@section('page-title')
    تعديل الموافقة
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تعديل الموافقة</h5>
                <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payroll-approvals.update', $approval->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">كشف الراتب</label>
                                <input type="text" class="form-control" value="{{ $approval->payroll->payroll_code }} - {{ $approval->payroll->employee->full_name }}" disabled>
                                <input type="hidden" name="payroll_id" value="{{ $approval->payroll_id }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستوى الموافقة <span class="text-danger">*</span></label>
                                <select class="form-select @error('approval_level') is-invalid @enderror" name="approval_level" required>
                                    <option value="">اختر المستوى</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('approval_level', $approval->approval_level) == $i ? 'selected' : '' }}>المستوى {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('approval_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموافق <span class="text-danger">*</span></label>
                                <select class="form-select @error('approver_id') is-invalid @enderror" name="approver_id" required>
                                    <option value="">اختر الموافق</option>
                                    @foreach ($approvers as $approver)
                                        <option value="{{ $approver->id }}" {{ old('approver_id', $approval->approver_id) == $approver->id ? 'selected' : '' }}>
                                            {{ $approver->name }} ({{ $approver->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('approver_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ترتيب الموافقة</label>
                                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $approval->sort_order) }}" min="0">
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

