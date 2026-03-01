@extends('admin.layouts.master')

@section('page-title')
    إضافة مناوبة جديدة
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
                <h5 class="page-title mb-0">إضافة مناوبة جديدة</h5>
                <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shifts.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">وقت البدء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       name="start_time" value="{{ old('start_time') }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">وقت الانتهاء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       name="end_time" value="{{ old('end_time') }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">المدة (ساعات) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" 
                                       name="duration_hours" value="{{ old('duration_hours', 8) }}" min="1" max="24" required>
                                @error('duration_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">فترة السماح للتأخير (بالدقائق)</label>
                                <input type="number" class="form-control" name="grace_period_minutes" value="{{ old('grace_period_minutes', 15) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مدة الاستراحة (بالدقائق)</label>
                                <input type="number" class="form-control" name="break_duration_minutes" value="{{ old('break_duration_minutes', 60) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">معدل الساعات الإضافية</label>
                                <input type="number" step="0.1" class="form-control" name="overtime_rate" value="{{ old('overtime_rate', 1.5) }}" min="1">
                                <small class="text-muted">مثال: 1.5 = 150%</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأدنى للساعات الإضافية (بالدقائق)</label>
                                <input type="number" class="form-control" name="overtime_threshold_minutes" value="{{ old('overtime_threshold_minutes', 0) }}" min="0">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">أيام العمل</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="monday" value="1" id="monday" {{ old('monday', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="monday">الاثنين</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tuesday" value="1" id="tuesday" {{ old('tuesday', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tuesday">الثلاثاء</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="wednesday" value="1" id="wednesday" {{ old('wednesday', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="wednesday">الأربعاء</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="thursday" value="1" id="thursday" {{ old('thursday', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="thursday">الخميس</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="friday" value="1" id="friday" {{ old('friday', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="friday">الجمعة</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="saturday" value="1" id="saturday" {{ old('saturday') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="saturday">السبت</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sunday" value="1" id="sunday" {{ old('sunday') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sunday">الأحد</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.shifts.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

