@extends('admin.layouts.master')

@section('page-title')
    إضافة استراحة جديدة
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
                <h5 class="page-title mb-0">إضافة استراحة جديدة</h5>
                <a href="{{ route('admin.attendance-breaks.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attendance-breaks.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">سجل الحضور <span class="text-danger">*</span></label>
                                <select class="form-select @error('attendance_id') is-invalid @enderror" name="attendance_id" required id="attendance_id">
                                    <option value="">اختر سجل الحضور</option>
                                    @foreach ($attendances as $att)
                                        <option value="{{ $att->id }}" 
                                                {{ old('attendance_id', $attendance?->id) == $att->id ? 'selected' : '' }}
                                                data-date="{{ $att->attendance_date }}">
                                            {{ $att->employee->full_name }} - {{ $att->attendance_date }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('attendance_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الاستراحة <span class="text-danger">*</span></label>
                                <select class="form-select @error('break_type') is-invalid @enderror" name="break_type" required>
                                    <option value="">اختر نوع الاستراحة</option>
                                    <option value="lunch" {{ old('break_type') == 'lunch' ? 'selected' : '' }}>غداء</option>
                                    <option value="coffee" {{ old('break_type') == 'coffee' ? 'selected' : '' }}>قهوة</option>
                                    <option value="prayer" {{ old('break_type') == 'prayer' ? 'selected' : '' }}>صلاة</option>
                                    <option value="other" {{ old('break_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('break_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">وقت بدء الاستراحة <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('break_start') is-invalid @enderror" 
                                       name="break_start" value="{{ old('break_start') }}" required>
                                @error('break_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">وقت انتهاء الاستراحة</label>
                                <input type="time" class="form-control @error('break_end') is-invalid @enderror" 
                                       name="break_end" value="{{ old('break_end') }}" id="break_end">
                                @error('break_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">سيتم حساب المدة تلقائياً عند إدخال وقت الانتهاء</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.attendance-breaks.index') }}" class="btn btn-secondary">إلغاء</a>
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
    const breakStart = document.querySelector('input[name="break_start"]');
    const breakEnd = document.querySelector('input[name="break_end"]');
    
    // التحقق من أن وقت الانتهاء بعد وقت البدء
    breakEnd.addEventListener('change', function() {
        if (breakStart.value && this.value) {
            if (this.value <= breakStart.value) {
                alert('وقت انتهاء الاستراحة يجب أن يكون بعد وقت البدء');
                this.value = '';
            }
        }
    });
});
</script>
@stop

