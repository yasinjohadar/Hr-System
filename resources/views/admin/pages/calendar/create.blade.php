@extends('admin.layouts.master')

@section('page-title')
    إضافة حدث جديد
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
                <h5 class="page-title mb-0">إضافة حدث جديد</h5>
                <a href="{{ route('admin.calendar-events.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.calendar-events.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">عنوان الحدث <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عنوان الحدث بالعربية</label>
                                <input type="text" class="form-control" name="title_ar" value="{{ old('title_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الحدث <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required id="event_type">
                                    <option value="">اختر نوع الحدث</option>
                                    <option value="personal" {{ old('type') == 'personal' ? 'selected' : '' }}>شخصي (للموظف الحالي)</option>
                                    <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>عام (للمؤسسة)</option>
                                    <option value="all" {{ old('type') == 'all' ? 'selected' : '' }}>للجميع</option>
                                    <option value="department" {{ old('type') == 'department' ? 'selected' : '' }}>لقسم معين</option>
                                    <option value="employee" {{ old('type') == 'employee' ? 'selected' : '' }}>لموظف معين</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="department_div" style="display: none;">
                                <label class="form-label">القسم <span class="text-danger">*</span></label>
                                <select class="form-select" name="department_id" id="department_id">
                                    <option value="">اختر القسم</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name_ar ?? $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6" id="employee_div" style="display: none;">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select class="form-select" name="employee_id" id="employee_id">
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ ووقت البدء <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                       name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ ووقت الانتهاء</label>
                                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                       name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">لون الحدث</label>
                                <input type="color" class="form-control form-control-color" name="color" value="{{ old('color', '#3b82f6') }}">
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_all_day" id="is_all_day" value="1" {{ old('is_all_day') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_all_day">
                                        حدث طوال اليوم
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_reminder" id="is_reminder" value="1" {{ old('is_reminder') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_reminder">
                                        تفعيل التذكير
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6" id="reminder_minutes_div" style="display: none;">
                                <label class="form-label">دقائق قبل التذكير</label>
                                <input type="number" class="form-control" name="reminder_minutes" value="{{ old('reminder_minutes', 15) }}" min="1">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.calendar-events.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const eventType = document.getElementById('event_type');
    const departmentDiv = document.getElementById('department_div');
    const employeeDiv = document.getElementById('employee_div');
    const isReminder = document.getElementById('is_reminder');
    const reminderMinutesDiv = document.getElementById('reminder_minutes_div');

    eventType.addEventListener('change', function() {
        if (this.value === 'department') {
            departmentDiv.style.display = 'block';
            employeeDiv.style.display = 'none';
            document.getElementById('department_id').required = true;
            document.getElementById('employee_id').required = false;
        } else if (this.value === 'employee') {
            departmentDiv.style.display = 'none';
            employeeDiv.style.display = 'block';
            document.getElementById('department_id').required = false;
            document.getElementById('employee_id').required = true;
        } else {
            departmentDiv.style.display = 'none';
            employeeDiv.style.display = 'none';
            document.getElementById('department_id').required = false;
            document.getElementById('employee_id').required = false;
        }
    });

    isReminder.addEventListener('change', function() {
        if (this.checked) {
            reminderMinutesDiv.style.display = 'block';
        } else {
            reminderMinutesDiv.style.display = 'none';
        }
    });

    // تشغيل عند التحميل
    eventType.dispatchEvent(new Event('change'));
    isReminder.dispatchEvent(new Event('change'));
});
</script>
@stop

