@extends('admin.layouts.master')

@section('page-title')
    جدولة صيانة جديدة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">جدولة صيانة جديدة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.asset-maintenances.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.asset-maintenances.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الأصل <span class="text-danger">*</span></label>
                                <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                                    <option value="">اختر الأصل</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->asset_code }} - {{ $asset->name_ar ?? $asset->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('asset_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">عنوان الصيانة <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title') }}" required placeholder="مثال: صيانة دورية، إصلاح عطل">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الصيانة <span class="text-danger">*</span></label>
                                <select name="maintenance_type" class="form-select @error('maintenance_type') is-invalid @enderror" required>
                                    <option value="preventive" {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>وقائية</option>
                                    <option value="corrective" {{ old('maintenance_type') == 'corrective' ? 'selected' : '' }}>تصحيحية</option>
                                    <option value="upgrade" {{ old('maintenance_type') == 'upgrade' ? 'selected' : '' }}>ترقية</option>
                                    <option value="cleaning" {{ old('maintenance_type') == 'cleaning' ? 'selected' : '' }}>تنظيف</option>
                                    <option value="inspection" {{ old('maintenance_type') == 'inspection' ? 'selected' : '' }}>فحص</option>
                                </select>
                                @error('maintenance_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                                    <option value="postponed" {{ old('status') == 'postponed' ? 'selected' : '' }}>مؤجلة</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الصيانة المجدول</label>
                                <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الصيانة الفعلي</label>
                                <input type="date" name="actual_date" class="form-control" value="{{ old('actual_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">التكلفة</label>
                                <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" step="0.01" min="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الصيانة القادمة</label>
                                <input type="date" name="next_maintenance_date" class="form-control" value="{{ old('next_maintenance_date') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مزود الخدمة</label>
                                <input type="text" name="service_provider" class="form-control" value="{{ old('service_provider') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">معلومات الاتصال بمزود الخدمة</label>
                                <input type="text" name="service_provider_contact" class="form-control" value="{{ old('service_provider_contact') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">من قام بالصيانة</label>
                                <select name="performed_by" class="form-select">
                                    <option value="">لا يوجد</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('performed_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.asset-maintenances.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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

