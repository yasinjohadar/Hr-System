@extends('admin.layouts.master')

@section('page-title')
    إنشاء عرض تعيين
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
                <h5 class="page-title mb-0">إنشاء عرض تعيين</h5>
                <a href="{{ route('admin.offer-letters.index') }}" class="btn btn-secondary">العودة للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.offer-letters.store') }}" enctype="multipart/form-data" id="offerForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">طلب التوظيف <span class="text-danger">*</span></label>
                                <select name="job_application_id" id="job_application_id" class="form-select @error('job_application_id') is-invalid @enderror" required>
                                    <option value="">-- اختر طلب التوظيف (مرشح + وظيفة) --</option>
                                    @foreach ($applications as $app)
                                        <option value="{{ $app->id }}"
                                            data-job-title="{{ $app->jobVacancy->title_ar ?? $app->jobVacancy->title }}"
                                            data-salary="{{ $app->expected_salary ?? $app->jobVacancy->min_salary ?? '' }}"
                                            data-start-date="{{ $app->available_start_date ? $app->available_start_date->format('Y-m-d') : ($app->jobVacancy->start_date ? $app->jobVacancy->start_date->format('Y-m-d') : '') }}"
                                            data-currency-id="{{ $app->jobVacancy->currency_id ?? '' }}"
                                            {{ (old('job_application_id') ?? $preselectedApplicationId) == $app->id ? 'selected' : '' }}>
                                            {{ $app->candidate->full_name }} — {{ $app->jobVacancy->title_ar ?? $app->jobVacancy->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_application_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المسمى الوظيفي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_title') is-invalid @enderror"
                                       name="job_title" id="job_title" value="{{ old('job_title') }}" required maxlength="255">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">الراتب</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('salary') is-invalid @enderror"
                                       name="salary" id="salary" value="{{ old('salary') }}">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">العملة</label>
                                <select name="currency_id" id="currency_id" class="form-select @error('currency_id') is-invalid @enderror">
                                    <option value="">-- اختياري --</option>
                                    @foreach ($currencies as $c)
                                        <option value="{{ $c->id }}" {{ old('currency_id') == $c->id ? 'selected' : '' }}>{{ $c->code }} ({{ $c->name }})</option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" id="start_date" value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">صلاحية العرض حتى</label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                                       name="valid_until" value="{{ old('valid_until') }}">
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">مرفق مستند العرض (PDF, Word, صورة)</label>
                                <input type="file" class="form-control @error('document_path') is-invalid @enderror"
                                       name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.offer-letters.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>حفظ عرض التعيين</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.getElementById('job_application_id').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    if (opt.value) {
        document.getElementById('job_title').value = opt.getAttribute('data-job-title') || '';
        document.getElementById('salary').value = opt.getAttribute('data-salary') || '';
        document.getElementById('start_date').value = opt.getAttribute('data-start-date') || '';
        document.getElementById('currency_id').value = opt.getAttribute('data-currency-id') || '';
    }
});
// Trigger once on load if preselected
var sel = document.getElementById('job_application_id');
if (sel.value) sel.dispatchEvent(new Event('change'));
</script>
@stop
