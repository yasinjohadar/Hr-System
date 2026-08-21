@extends('admin.layouts.master')

@section('page-title')
    تفويض جديد
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid admin-page-shell">
            @include('admin.pages.users.partials.alerts')

            <div class="admin-page-banner">
                <div class="admin-page-banner-main">
                    <span class="admin-page-banner-icon"><i class="ri-add-circle-line"></i></span>
                    <div class="admin-page-banner-text">
                        <h1>تفويض جديد</h1>
                        <p>إنشاء تفويض صلاحيات موافقة لموظف آخر خلال فترة محدّدة</p>
                    </div>
                </div>
                <div class="admin-page-banner-actions">
                    <a href="{{ route('admin.team.delegations.index') }}" class="admin-btn admin-btn-light">
                        <i class="ri-arrow-right-line"></i>
                        العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-8">
                    <div class="admin-page-card">
                        <div class="card-toolbar">
                            <h6 class="mb-0 fw-bold">بيانات التفويض</h6>
                        </div>

                        <form class="admin-form" method="POST" action="{{ route('admin.team.delegations.store') }}">
                            @csrf

                            <div class="admin-form-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="admin-form-label">المفوَّض <span class="text-danger">*</span></label>
                                        <select name="delegate_id" class="form-select @error('delegate_id') is-invalid @enderror" required>
                                            <option value="">اختر المفوَّض</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" @selected(old('delegate_id') == $user->id)>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('delegate_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="admin-form-label mb-1">أنواع الطلبات المفوَّضة</label>
                                        <p class="text-muted small mb-2">اتركها فارغة لتفويض جميع الأنواع</p>
                                        <div class="row g-2">
                                            @foreach ($workflowTypes as $key => $label)
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input type="checkbox" name="workflow_types[]" value="{{ $key }}"
                                                               class="form-check-input" id="type_{{ $key }}"
                                                               @checked(in_array($key, old('workflow_types', [])))>
                                                        <label class="form-check-label" for="type_{{ $key }}">{{ $label }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="admin-form-label">تاريخ البدء <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="start_date"
                                               class="form-control @error('start_date') is-invalid @enderror"
                                               value="{{ old('start_date') }}" required>
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="admin-form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="end_date"
                                               class="form-control @error('end_date') is-invalid @enderror"
                                               value="{{ old('end_date') }}" required>
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="admin-form-label">ملاحظات</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                                  rows="3" placeholder="سبب التفويض أو أي ملاحظات...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="admin-form-footer">
                                <a href="{{ route('admin.team.delegations.index') }}" class="admin-btn admin-btn-secondary">إلغاء</a>
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="ri-save-line"></i>
                                    حفظ التفويض
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
