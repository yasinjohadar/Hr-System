@extends('admin.layouts.master')

@section('page-title')
    توزيع أصل جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">توزيع أصل جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.asset-assignments.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الأصل <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required id="asset_id">
                                    <option value="">اختر الأصل</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}
                                                data-status="{{ $asset->status }}"
                                                data-available="{{ $asset->isAvailable() ? '1' : '0' }}">
                                            {{ $asset->asset_code }} - {{ $asset->name_ar ?? $asset->name }}
                                            @if (!$asset->isAvailable())
                                                (غير متاح)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">سيتم عرض الأصول المتاحة فقط</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror" required>
                                    <option value="">اختر الموظف</option>
                                    @foreach ($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->full_name }} ({{ $emp->employee_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ التوزيع <span class="text-danger">*</span></label>
                                <input type="date" name="assigned_date" class="form-control @error('assigned_date') is-invalid @enderror" 
                                       value="{{ old('assigned_date', date('Y-m-d')) }}" required>
                                @error('assigned_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاسترجاع المتوقع</label>
                                <input type="date" name="expected_return_date" class="form-control" 
                                       value="{{ old('expected_return_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">حالة الأصل عند التوزيع <span class="text-danger">*</span></label>
                                <select name="condition_on_assignment" class="form-select @error('condition_on_assignment') is-invalid @enderror" required>
                                    <option value="excellent" {{ old('condition_on_assignment') == 'excellent' ? 'selected' : '' }}>ممتاز</option>
                                    <option value="good" {{ old('condition_on_assignment', 'good') == 'good' ? 'selected' : '' }}>جيد</option>
                                    <option value="fair" {{ old('condition_on_assignment') == 'fair' ? 'selected' : '' }}>متوسط</option>
                                    <option value="poor" {{ old('condition_on_assignment') == 'poor' ? 'selected' : '' }}>ضعيف</option>
                                </select>
                                @error('condition_on_assignment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات التوزيع</label>
                                <textarea name="assignment_notes" class="form-control" rows="3">{{ old('assignment_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.asset-assignments.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // فلترة الأصول المتاحة فقط
        document.getElementById('asset_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const isAvailable = selectedOption.getAttribute('data-available') === '1';
            
            if (!isAvailable && this.value) {
                alert('هذا الأصل غير متاح للتوزيع. يرجى اختيار أصل آخر.');
                this.value = '';
            }
        });
    </script>
@stop


