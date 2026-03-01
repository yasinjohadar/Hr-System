@extends('admin.layouts.master')

@section('page-title')
    إضافة موافقة جديدة
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
                <h5 class="page-title mb-0">إضافة موافقة جديدة</h5>
                <a href="{{ route('admin.payroll-approvals.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.payroll-approvals.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">كشف الراتب <span class="text-danger">*</span></label>
                                <select class="form-select @error('payroll_id') is-invalid @enderror" name="payroll_id" required>
                                    <option value="">اختر كشف الراتب</option>
                                    @foreach ($payrolls as $p)
                                        <option value="{{ $p->id }}" {{ old('payroll_id', $payroll?->id) == $p->id ? 'selected' : '' }}>
                                            {{ $p->payroll_code }} - {{ $p->employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payroll_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مستوى الموافقة <span class="text-danger">*</span></label>
                                <select class="form-select @error('approval_level') is-invalid @enderror" name="approval_level" required>
                                    <option value="">اختر المستوى</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('approval_level') == $i ? 'selected' : '' }}>المستوى {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('approval_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">المستوى 1 هو أول موافقة، والمستوى 2 هو ثاني موافقة، وهكذا...</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموافق <span class="text-danger">*</span></label>
                                <select class="form-select @error('approver_id') is-invalid @enderror" name="approver_id" required>
                                    <option value="">اختر الموافق</option>
                                    @foreach ($approvers as $approver)
                                        <option value="{{ $approver->id }}" {{ old('approver_id') == $approver->id ? 'selected' : '' }}>
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
                                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                <small class="text-muted">يستخدم لترتيب الموافقات عند عرضها</small>
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

