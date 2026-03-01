@extends('admin.layouts.master')

@section('page-title')
    جدولة مقابلة جديدة
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
                <h5 class="page-title mb-0">جدولة مقابلة جديدة</h5>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.interviews.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select @error('job_application_id') is-invalid @enderror" 
                                            name="job_application_id" id="job_application_id" required>
                                        <option value="">اختر طلب التوظيف</option>
                                        @foreach ($applications as $application)
                                            <option value="{{ $application->id }}" {{ old('job_application_id') == $application->id ? 'selected' : '' }}>
                                                {{ $application->candidate->full_name }} - {{ $application->jobVacancy->title_ar ?? $application->jobVacancy->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label>طلب التوظيف <span class="text-danger">*</span></label>
                                    @error('job_application_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            name="type" id="type" required>
                                        <option value="phone" {{ old('type', 'in_person') == 'phone' ? 'selected' : '' }}>هاتفية</option>
                                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>فيديو</option>
                                        <option value="in_person" {{ old('type', 'in_person') == 'in_person' ? 'selected' : '' }}>شخصية</option>
                                        <option value="panel" {{ old('type') == 'panel' ? 'selected' : '' }}>لجنة</option>
                                        <option value="technical" {{ old('type') == 'technical' ? 'selected' : '' }}>تقنية</option>
                                        <option value="hr" {{ old('type') == 'hr' ? 'selected' : '' }}>موارد بشرية</option>
                                        <option value="final" {{ old('type') == 'final' ? 'selected' : '' }}>نهائية</option>
                                    </select>
                                    <label>نوع المقابلة <span class="text-danger">*</span></label>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select @error('round') is-invalid @enderror" 
                                            name="round" id="round" required>
                                        <option value="first" {{ old('round', 'first') == 'first' ? 'selected' : '' }}>الأولى</option>
                                        <option value="second" {{ old('round') == 'second' ? 'selected' : '' }}>الثانية</option>
                                        <option value="third" {{ old('round') == 'third' ? 'selected' : '' }}>الثالثة</option>
                                        <option value="final" {{ old('round') == 'final' ? 'selected' : '' }}>النهائية</option>
                                    </select>
                                    <label>جولة المقابلة <span class="text-danger">*</span></label>
                                    @error('round')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="date" class="form-control @error('interview_date') is-invalid @enderror" 
                                           name="interview_date" placeholder="تاريخ المقابلة" 
                                           value="{{ old('interview_date', date('Y-m-d')) }}" required>
                                    <label>تاريخ المقابلة <span class="text-danger">*</span></label>
                                    @error('interview_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="time" class="form-control @error('interview_time') is-invalid @enderror" 
                                           name="interview_time" placeholder="وقت المقابلة" 
                                           value="{{ old('interview_time', '09:00') }}" required>
                                    <label>وقت المقابلة <span class="text-danger">*</span></label>
                                    @error('interview_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" 
                                           name="duration" placeholder="المدة بالدقائق" 
                                           value="{{ old('duration', 60) }}" min="15">
                                    <label>المدة (دقيقة)</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" 
                                           name="location" placeholder="المكان" value="{{ old('location') }}">
                                    <label>المكان</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="url" class="form-control" 
                                           name="meeting_link" placeholder="رابط الاجتماع" value="{{ old('meeting_link') }}">
                                    <label>رابط الاجتماع (للمقابلات عن بُعد)</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" name="address" placeholder="العنوان الكامل" style="height: 80px">{{ old('address') }}</textarea>
                                    <label>العنوان الكامل</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.interviews.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ المقابلة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


