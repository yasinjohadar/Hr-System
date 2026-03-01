@extends('admin.layouts.master')

@section('page-title')
    تعديل المناوبة
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
                <h5 class="page-title mb-0">تعديل المناوبة</h5>
                <a href="{{ route('admin.shifts.show', $shift->id) }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shifts.update', $shift->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $shift->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $shift->name_ar) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">وقت البدء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">وقت الانتهاء <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">المدة (ساعات) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" 
                                       name="duration_hours" value="{{ old('duration_hours', $shift->duration_hours) }}" min="1" max="24" required>
                                @error('duration_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">فترة السماح للتأخير (بالدقائق)</label>
                                <input type="number" class="form-control" name="grace_period_minutes" value="{{ old('grace_period_minutes', $shift->grace_period_minutes) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مدة الاستراحة (بالدقائق)</label>
                                <input type="number" class="form-control" name="break_duration_minutes" value="{{ old('break_duration_minutes', $shift->break_duration_minutes) }}" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">معدل الساعات الإضافية</label>
                                <input type="number" step="0.1" class="form-control" name="overtime_rate" value="{{ old('overtime_rate', $shift->overtime_rate) }}" min="1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأدنى للساعات الإضافية (بالدقائق)</label>
                                <input type="number" class="form-control" name="overtime_threshold_minutes" value="{{ old('overtime_threshold_minutes', $shift->overtime_threshold_minutes) }}" min="0">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">أيام العمل</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="monday" value="1" id="monday" {{ old('monday', $shift->monday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="monday">الاثنين</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="tuesday" value="1" id="tuesday" {{ old('tuesday', $shift->tuesday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="tuesday">الثلاثاء</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="wednesday" value="1" id="wednesday" {{ old('wednesday', $shift->wednesday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="wednesday">الأربعاء</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="thursday" value="1" id="thursday" {{ old('thursday', $shift->thursday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="thursday">الخميس</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="friday" value="1" id="friday" {{ old('friday', $shift->friday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="friday">الجمعة</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="saturday" value="1" id="saturday" {{ old('saturday', $shift->saturday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="saturday">السبت</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="sunday" value="1" id="sunday" {{ old('sunday', $shift->sunday) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="sunday">الأحد</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $shift->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $shift->description) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.shifts.show', $shift->id) }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

