@extends('admin.layouts.master')

@section('page-title')
    تعديل الاجتماع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل الاجتماع</h5>
                </div>
                <div>
                    <a href="{{ route('admin.meetings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.meetings.update', $meeting->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $meeting->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العنوان (عربي)</label>
                                <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $meeting->title_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ ووقت البدء <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror" 
                                       value="{{ old('start_time', $meeting->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ ووقت الانتهاء <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror" 
                                       value="{{ old('end_time', $meeting->end_time->format('Y-m-d\TH:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الاجتماع <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                                    <option value="in_person" {{ old('type', $meeting->type) == 'in_person' ? 'selected' : '' }}>حضوري</option>
                                    <option value="virtual" {{ old('type', $meeting->type) == 'virtual' ? 'selected' : '' }}>افتراضي</option>
                                    <option value="hybrid" {{ old('type', $meeting->type) == 'hybrid' ? 'selected' : '' }}>مختلط</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">المنظم</label>
                                <select class="form-select" name="organizer_id">
                                    <option value="">اختر المنظم</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ old('organizer_id', $meeting->organizer_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموقع</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location', $meeting->location) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">رابط الاجتماع (للمواعيد الافتراضية)</label>
                                <input type="url" name="meeting_link" class="form-control" value="{{ old('meeting_link', $meeting->meeting_link) }}" placeholder="https://...">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $meeting->description) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">جدول الأعمال</label>
                                <textarea name="agenda" class="form-control" rows="4">{{ old('agenda', $meeting->agenda) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الحضور</label>
                                <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                    <div class="mb-2">
                                        <input type="checkbox" id="select-all" class="form-check-input">
                                        <label for="select-all" class="form-check-label ms-2">تحديد الكل</label>
                                    </div>
                                    <hr>
                                    @php
                                        $existingAttendees = $meeting->attendees->pluck('employee_id')->toArray();
                                        $requiredAttendees = $meeting->attendees->where('is_required', true)->pluck('employee_id')->toArray();
                                    @endphp
                                    @foreach ($employees as $employee)
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="attendees[]" value="{{ $employee->id }}" 
                                                   id="attendee_{{ $employee->id }}" class="form-check-input attendee-checkbox"
                                                   {{ (old('attendees') && in_array($employee->id, old('attendees'))) || in_array($employee->id, $existingAttendees) ? 'checked' : '' }}>
                                            <label for="attendee_{{ $employee->id }}" class="form-check-label">
                                                {{ $employee->full_name }} ({{ $employee->employee_code }})
                                            </label>
                                            <input type="checkbox" name="required_attendees[]" value="{{ $employee->id }}" 
                                                   id="required_{{ $employee->id }}" class="form-check-input ms-3"
                                                   {{ (old('required_attendees') && in_array($employee->id, old('required_attendees'))) || in_array($employee->id, $requiredAttendees) ? 'checked' : '' }}>
                                            <label for="required_{{ $employee->id }}" class="form-check-label small">مطلوب</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.meetings.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.attendee-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@stop

