@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب توظيف جديد
@stop

@section('css')
    <style>
        .form-floating label {
            right: auto;
            left: 0.75rem;
        }
    </style>
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
                <h5 class="page-title mb-0">إضافة طلب توظيف جديد</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.job-applications.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('job_vacancy_id') is-invalid @enderror" 
                                            name="job_vacancy_id" id="job_vacancy_id" required>
                                        <option value="">اختر الوظيفة الشاغرة</option>
                                        @foreach ($vacancies as $vacancy)
                                            <option value="{{ $vacancy->id }}" {{ old('job_vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                                                {{ $vacancy->title_ar ?? $vacancy->title }} ({{ $vacancy->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>الوظيفة الشاغرة <span class="text-danger">*</span></label>
                                    @error('job_vacancy_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('candidate_id') is-invalid @enderror" 
                                            name="candidate_id" id="candidate_id" required>
                                        <option value="">اختر المرشح</option>
                                        @foreach ($candidates as $candidate)
                                            <option value="{{ $candidate->id }}" {{ old('candidate_id') == $candidate->id ? 'selected' : '' }}>
                                                {{ $candidate->full_name }} ({{ $candidate->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>المرشح <span class="text-danger">*</span></label>
                                    @error('candidate_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('application_date') is-invalid @enderror" 
                                           name="application_date" placeholder="تاريخ التقديم" 
                                           value="{{ old('application_date', date('Y-m-d')) }}" required>
                                    <label>تاريخ التقديم <span class="text-danger">*</span></label>
                                    @error('application_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select @error('source') is-invalid @enderror" 
                                            name="source" id="source" required>
                                        <option value="website" {{ old('source', 'website') == 'website' ? 'selected' : '' }}>الموقع الإلكتروني</option>
                                        <option value="linkedin" {{ old('source') == 'linkedin' ? 'selected' : '' }}>لينكد إن</option>
                                        <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>إحالة</option>
                                        <option value="indeed" {{ old('source') == 'indeed' ? 'selected' : '' }}>إنديد</option>
                                        <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                    <label>مصدر التقديم <span class="text-danger">*</span></label>
                                    @error('source')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" 
                                           name="cv_path" accept=".pdf,.doc,.docx">
                                    <label>السيرة الذاتية (PDF, DOC, DOCX)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="file" class="form-control" 
                                           name="cover_letter_path" accept=".pdf,.doc,.docx">
                                    <label>خطاب التقديم (PDF, DOC, DOCX)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="notes" placeholder="ملاحظات" style="height: 100px">{{ old('notes') }}</textarea>
                                    <label>ملاحظات</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.job-applications.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الطلب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


