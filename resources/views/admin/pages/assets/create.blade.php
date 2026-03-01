@extends('admin.layouts.master')

@section('page-title')
    إضافة أصل جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة أصل جديد</h5>
                </div>
                <div>
                    <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.assets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الأصل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">اسم الأصل (عربي)</label>
                                <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الفئة <span class="text-danger">*</span></label>
                                <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                    <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                    <option value="office" {{ old('category') == 'office' ? 'selected' : '' }}>مكتبي</option>
                                    <option value="mobile" {{ old('category') == 'mobile' ? 'selected' : '' }}>متنقل</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">النوع</label>
                                <input type="text" name="type" class="form-control" value="{{ old('type') }}" 
                                       placeholder="مثال: لابتوب، هاتف، طابعة">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الشركة المصنعة</label>
                                <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموديل</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الرقم التسلسلي</label>
                                <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الباركود</label>
                                <input type="text" name="barcode" class="form-control" value="{{ old('barcode') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الشراء</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تكلفة الشراء</label>
                                <input type="number" name="purchase_cost" class="form-control" 
                                       value="{{ old('purchase_cost') }}" step="0.01" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">القيمة الحالية</label>
                                <input type="number" name="current_value" class="form-control" 
                                       value="{{ old('current_value') }}" step="0.01" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>متاح</option>
                                    <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>موزع</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>قيد الصيانة</option>
                                    <option value="damaged" {{ old('status') == 'damaged' ? 'selected' : '' }}>معطل</option>
                                    <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>مفقود</option>
                                    <option value="disposed" {{ old('status') == 'disposed' ? 'selected' : '' }}>مستبعد</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الفرع</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">اختر الفرع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">القسم</label>
                                <select name="department_id" class="form-select">
                                    <option value="">اختر القسم</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموقع</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}" 
                                       placeholder="مثال: مكتب 101، غرفة 205">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">انتهاء الضمان</label>
                                <input type="date" name="warranty_expiry" class="form-control" value="{{ old('warranty_expiry') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">صورة الأصل</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">الحد الأقصى: 2MB</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


