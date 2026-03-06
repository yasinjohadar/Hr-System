@extends('admin.layouts.master')

@section('page-title')
    تعديل عرض التعيين
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
                <h5 class="page-title mb-0">تعديل عرض التعيين</h5>
                <a href="{{ route('admin.offer-letters.show', $offer_letter) }}" class="btn btn-secondary">العودة للعرض</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.offer-letters.update', $offer_letter) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-12">
                                <p class="text-muted mb-0">المرشح: <strong>{{ $offer_letter->jobApplication->candidate->full_name }}</strong> — الوظيفة: <strong>{{ $offer_letter->jobApplication->jobVacancy->title ?? $offer_letter->jobApplication->jobVacancy->title_ar }}</strong></p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المسمى الوظيفي <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('job_title') is-invalid @enderror"
                                       name="job_title" value="{{ old('job_title', $offer_letter->job_title) }}" required maxlength="255">
                                @error('job_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">الراتب</label>
                                <input type="number" step="0.01" min="0" class="form-control @error('salary') is-invalid @enderror"
                                       name="salary" value="{{ old('salary', $offer_letter->salary) }}">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">العملة</label>
                                <select name="currency_id" class="form-select @error('currency_id') is-invalid @enderror">
                                    <option value="">-- اختياري --</option>
                                    @foreach ($currencies as $c)
                                        <option value="{{ $c->id }}" {{ old('currency_id', $offer_letter->currency_id) == $c->id ? 'selected' : '' }}>{{ $c->code }}</option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                       name="start_date" value="{{ old('start_date', $offer_letter->start_date?->format('Y-m-d')) }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">صلاحية العرض حتى</label>
                                <input type="date" class="form-control @error('valid_until') is-invalid @enderror"
                                       name="valid_until" value="{{ old('valid_until', $offer_letter->valid_until?->format('Y-m-d')) }}">
                                @error('valid_until')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $offer_letter->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">مرفق مستند العرض</label>
                                @if($offer_letter->document_path)
                                    <p class="small text-muted mb-1">مرفق حالي: <a href="{{ asset('storage/' . $offer_letter->document_path) }}" target="_blank">عرض الملف</a></p>
                                @endif
                                <input type="file" class="form-control @error('document_path') is-invalid @enderror"
                                       name="document_path" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                @error('document_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.offer-letters.show', $offer_letter) }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i>حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
