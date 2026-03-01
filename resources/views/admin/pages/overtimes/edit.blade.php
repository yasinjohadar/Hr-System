@extends('admin.layouts.master')

@section('page-title')
    تعديل الساعات الإضافية
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
                <h5 class="page-title mb-0">تعديل الساعات الإضافية</h5>
                <a href="{{ route('admin.overtimes.show', $overtime->id) }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.overtimes.update', $overtime->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $overtime->employee->full_name }}" disabled>
                                <small class="text-muted">لا يمكن تغيير الموظف</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">التاريخ</label>
                                <input type="text" class="form-control" value="{{ $overtime->overtime_date->format('Y-m-d') }}" disabled>
                                <small class="text-muted">لا يمكن تغيير التاريخ</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">من <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                                       name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($overtime->start_time)->format('H:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">إلى <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                                       name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($overtime->end_time)->format('H:i')) }}" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select class="form-select @error('overtime_type') is-invalid @enderror" name="overtime_type" required>
                                    <option value="regular" {{ old('overtime_type', $overtime->overtime_type) == 'regular' ? 'selected' : '' }}>عادي</option>
                                    <option value="holiday" {{ old('overtime_type', $overtime->overtime_type) == 'holiday' ? 'selected' : '' }}>عطلة</option>
                                    <option value="night" {{ old('overtime_type', $overtime->overtime_type) == 'night' ? 'selected' : '' }}>ليلي</option>
                                    <option value="weekend" {{ old('overtime_type', $overtime->overtime_type) == 'weekend' ? 'selected' : '' }}>عطلة نهاية الأسبوع</option>
                                </select>
                                @error('overtime_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">معدل الضرب</label>
                                <input type="number" step="0.1" class="form-control" name="rate_multiplier" value="{{ old('rate_multiplier', $overtime->rate_multiplier) }}" min="1">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">السبب</label>
                                <textarea class="form-control" name="reason" rows="3">{{ old('reason', $overtime->reason) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.overtimes.show', $overtime->id) }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

