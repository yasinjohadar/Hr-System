@extends('admin.layouts.master')

@section('page-title')
    تعديل الشهادة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل الشهادة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-certificates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-certificates.update', $certificate->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $certificate->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم الشهادة <span class="text-danger">*</span></label>
                                <input type="text" name="certificate_name" class="form-control @error('certificate_name') is-invalid @enderror" 
                                       value="{{ old('certificate_name', $certificate->certificate_name) }}" required>
                                @error('certificate_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم الشهادة (عربي)</label>
                                <input type="text" name="certificate_name_ar" class="form-control" 
                                       value="{{ old('certificate_name_ar', $certificate->certificate_name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الجهة المانحة <span class="text-danger">*</span></label>
                                <input type="text" name="issuing_organization" class="form-control @error('issuing_organization') is-invalid @enderror" 
                                       value="{{ old('issuing_organization', $certificate->issuing_organization) }}" required>
                                @error('issuing_organization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رقم الشهادة</label>
                                <input type="text" name="certificate_number" class="form-control" 
                                       value="{{ old('certificate_number', $certificate->certificate_number) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                                <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" 
                                       value="{{ old('issue_date', $certificate->issue_date->format('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ انتهاء الصلاحية</label>
                                <input type="date" name="expiry_date" class="form-control" 
                                       value="{{ old('expiry_date', $certificate->expiry_date?->format('Y-m-d')) }}" 
                                       id="expiry_date" {{ $certificate->does_not_expire ? 'disabled' : '' }}>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="does_not_expire" class="form-check-input" value="1" 
                                           id="does_not_expire" {{ old('does_not_expire', $certificate->does_not_expire) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="does_not_expire">لا تنتهي صلاحيتها</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملف الشهادة الجديد (اختياري)</label>
                                <input type="file" name="file_path" class="form-control" 
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @if ($certificate->file_path)
                                    <small class="text-muted">الملف الحالي موجود</small>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $certificate->description) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-certificates.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('does_not_expire').addEventListener('change', function() {
            const expiryDate = document.getElementById('expiry_date');
            if (this.checked) {
                expiryDate.disabled = true;
                expiryDate.value = '';
            } else {
                expiryDate.disabled = false;
            }
        });
    </script>
@stop


