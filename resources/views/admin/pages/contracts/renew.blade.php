@extends('admin.layouts.master')

@section('page-title')
    تجديد العقد
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header d-flex justify-content-between align-items-center my-4">
                <h5 class="page-title mb-0">تجديد عقد — {{ $contract->employee->full_name ?? $contract->employee->employee_code }}</h5>
                <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-secondary">إلغاء / العودة</a>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-0 text-muted">العقد الحالي ينتهي في <strong>{{ $contract->end_date->format('Y-m-d') }}</strong>. أدخل بيانات العقد الجديد أدناه.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.contracts.store-renew', $contract) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $contract->employee->full_name ?? $contract->employee->employee_code }}" readonly disabled>
                                <input type="hidden" name="employee_id" value="{{ $contract->employee_id }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع العقد الجديد <span class="text-danger">*</span></label>
                                <select name="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                                    <option value="fixed_term" {{ old('contract_type', $contract->contract_type) == 'fixed_term' ? 'selected' : '' }}>محدد المدة</option>
                                    <option value="permanent" {{ old('contract_type', $contract->contract_type) == 'permanent' ? 'selected' : '' }}>دائم</option>
                                    <option value="trial" {{ old('contract_type', $contract->contract_type) == 'trial' ? 'selected' : '' }}>تجريبي</option>
                                    <option value="project" {{ old('contract_type', $contract->contract_type) == 'project' ? 'selected' : '' }}>مشروع</option>
                                </select>
                                @error('contract_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" value="{{ old('start_date', $contract->end_date->copy()->addDay()->format('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                       name="end_date" value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                          rows="2">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">مرفق المستند (اختياري)</label>
                                <input type="file" class="form-control @error('document_path') is-invalid @enderror"
                                       name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-success px-4"><i class="fas fa-rotate-right me-2"></i>تجديد العقد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
