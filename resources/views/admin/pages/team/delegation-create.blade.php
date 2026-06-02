@extends('admin.layouts.master')

@section('page-title')
    تفويض جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">تفويض جديد</h5>
                    <p class="text-muted fs-13 mb-0">إنشاء تفويض صلاحيات موافقة</p>
                </div>
                <a href="{{ route('admin.team.delegations.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>العودة
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="card custom-card">
                        <div class="card-header">
                            <h6 class="card-title fw-semibold">بيانات التفويض</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.team.delegations.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">المفوَّض <span class="text-danger">*</span></label>
                                    <select name="delegate_id" class="form-select @error('delegate_id') is-invalid @enderror" required>
                                        <option value="">اختر المفوَّض</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('delegate_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('delegate_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">أنواع الطلبات المفوَّضة</label>
                                    <p class="text-muted fs-13 mb-2">اتركها فارغة لتفويض جميع الأنواع</p>
                                    <div class="row">
                                        @foreach($workflowTypes as $key => $label)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input type="checkbox" name="workflow_types[]" value="{{ $key }}" 
                                                           class="form-check-input" id="type_{{ $key }}"
                                                           {{ in_array($key, old('workflow_types', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="type_{{ $key }}">{{ $label }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">تاريخ البدء <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="start_date" 
                                               class="form-control @error('start_date') is-invalid @enderror"
                                               value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="end_date" 
                                               class="form-control @error('end_date') is-invalid @enderror"
                                               value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">ملاحظات</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                              rows="3" placeholder="سبب التفويض أو أي ملاحظات...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-save-line me-1"></i>حفظ التفويض
                                    </button>
                                    <a href="{{ route('admin.team.delegations.index') }}" class="btn btn-outline-secondary">
                                        إلغاء
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
