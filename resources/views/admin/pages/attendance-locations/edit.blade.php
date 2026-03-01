@extends('admin.layouts.master')

@section('page-title')
    تعديل موقع الحضور
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
                <h5 class="page-title mb-0">تعديل موقع الحضور</h5>
                <a href="{{ route('admin.attendance-locations.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attendance-locations.update', $location->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name', $location->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $location->name_ar) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الكود</label>
                                <input type="text" class="form-control" name="code" value="{{ old('code', $location->code) }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">العنوان</label>
                                <input type="text" class="form-control" name="address" value="{{ old('address', $location->address) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">خط العرض (Latitude) <span class="text-danger">*</span></label>
                                <input type="number" step="0.00000001" class="form-control @error('latitude') is-invalid @enderror" 
                                       name="latitude" value="{{ old('latitude', $location->latitude) }}" required min="-90" max="90">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">خط الطول (Longitude) <span class="text-danger">*</span></label>
                                <input type="number" step="0.00000001" class="form-control @error('longitude') is-invalid @enderror" 
                                       name="longitude" value="{{ old('longitude', $location->longitude) }}" required min="-180" max="180">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">نصف القطر (بالمتر) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('radius_meters') is-invalid @enderror" 
                                       name="radius_meters" value="{{ old('radius_meters', $location->radius_meters) }}" required min="10" max="10000">
                                @error('radius_meters')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description', $location->description) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="require_location" id="require_location" value="1" {{ old('require_location', $location->require_location) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require_location">
                                        يتطلب التحقق من الموقع
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <h6 class="mt-3 mb-2">الموظفون المسموح لهم</h6>
                                <select class="form-select" name="allowed_employees[]" multiple size="5">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ in_array($employee->id, old('allowed_employees', $location->allowed_employees ?? [])) ? 'selected' : '' }}>
                                            {{ $employee->full_name }} - {{ $employee->employee_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mt-3 mb-2">الأقسام المسموحة</h6>
                                <select class="form-select" name="allowed_departments[]" multiple size="5">
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ in_array($department->id, old('allowed_departments', $location->allowed_departments ?? [])) ? 'selected' : '' }}>
                                            {{ $department->name_ar ?? $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <h6 class="mt-3 mb-2">المناصب المسموحة</h6>
                                <select class="form-select" name="allowed_positions[]" multiple size="5">
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" {{ in_array($position->id, old('allowed_positions', $location->allowed_positions ?? [])) ? 'selected' : '' }}>
                                            {{ $position->name_ar ?? $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.attendance-locations.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

