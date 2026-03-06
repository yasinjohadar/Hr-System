@extends('admin.layouts.master')

@section('page-title')
    إضافة عقد جديد
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
                <h5 class="page-title mb-0">إضافة عقد جديد</h5>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.contracts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">-- اختر الموظف --</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع العقد <span class="text-danger">*</span></label>
                                <select name="contract_type" class="form-select @error('contract_type') is-invalid @enderror" required>
                                    <option value="fixed_term" {{ old('contract_type') == 'fixed_term' ? 'selected' : '' }}>محدد المدة</option>
                                    <option value="permanent" {{ old('contract_type') == 'permanent' ? 'selected' : '' }}>دائم</option>
                                    <option value="trial" {{ old('contract_type') == 'trial' ? 'selected' : '' }}>تجريبي</option>
                                    <option value="project" {{ old('contract_type') == 'project' ? 'selected' : '' }}>مشروع</option>
                                </select>
                                @error('contract_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" value="{{ old('start_date') }}" required>
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

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>منتهي</option>
                                    <option value="renewed" {{ old('status') == 'renewed' ? 'selected' : '' }}>تم تجديده</option>
                                    <option value="terminated" {{ old('status') == 'terminated' ? 'selected' : '' }}>منهي</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مرفق المستند (PDF, Word, صورة)</label>
                                <input type="file" class="form-control @error('document_path') is-invalid @enderror"
                                       name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes"
                                          rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>حفظ العقد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
