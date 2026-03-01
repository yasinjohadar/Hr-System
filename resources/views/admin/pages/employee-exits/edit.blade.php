@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب إنهاء الخدمة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل طلب إنهاء الخدمة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-exits.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.employee-exits.update', $exit->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الموظف</label>
                                <input type="text" class="form-control" value="{{ $exit->employee->full_name }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع إنهاء الخدمة <span class="text-danger">*</span></label>
                                <select name="exit_type" class="form-select @error('exit_type') is-invalid @enderror" required>
                                    <option value="resignation" {{ old('exit_type', $exit->exit_type) == 'resignation' ? 'selected' : '' }}>استقالة</option>
                                    <option value="termination" {{ old('exit_type', $exit->exit_type) == 'termination' ? 'selected' : '' }}>إنهاء خدمة</option>
                                    <option value="retirement" {{ old('exit_type', $exit->exit_type) == 'retirement' ? 'selected' : '' }}>تقاعد</option>
                                    <option value="end_of_contract" {{ old('exit_type', $exit->exit_type) == 'end_of_contract' ? 'selected' : '' }}>انتهاء عقد</option>
                                    <option value="other" {{ old('exit_type', $exit->exit_type) == 'other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('exit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاستقالة <span class="text-danger">*</span></label>
                                <input type="date" name="resignation_date" class="form-control @error('resignation_date') is-invalid @enderror" 
                                       value="{{ old('resignation_date', $exit->resignation_date->format('Y-m-d')) }}" required>
                                @error('resignation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">آخر يوم عمل <span class="text-danger">*</span></label>
                                <input type="date" name="last_working_day" class="form-control @error('last_working_day') is-invalid @enderror" 
                                       value="{{ old('last_working_day', $exit->last_working_day->format('Y-m-d')) }}" required>
                                @error('last_working_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', $exit->status) == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                                    <option value="in_process" {{ old('status', $exit->status) == 'in_process' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="completed" {{ old('status', $exit->status) == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                    <option value="cancelled" {{ old('status', $exit->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تسليم المهام إلى</label>
                                <select name="handover_to" class="form-select">
                                    <option value="">لا يوجد</option>
                                    @foreach ($employees as $emp)
                                        @if ($emp->id != $exit->employee_id)
                                            <option value="{{ $emp->id }}" {{ old('handover_to', $exit->handover_to) == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->full_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="assets_returned" class="form-check-input" value="1" 
                                           {{ old('assets_returned', $exit->assets_returned) ? 'checked' : '' }}>
                                    <label class="form-check-label">تم استرجاع الأصول</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="handover_completed" class="form-check-input" value="1" 
                                           {{ old('handover_completed', $exit->handover_completed) ? 'checked' : '' }}>
                                    <label class="form-check-label">تم تسليم المهام</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="documents_returned" class="form-check-input" value="1" 
                                           {{ old('documents_returned', $exit->documents_returned) ? 'checked' : '' }}>
                                    <label class="form-check-label">تم استرجاع المستندات</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="final_settlement_completed" class="form-check-input" value="1" 
                                           {{ old('final_settlement_completed', $exit->final_settlement_completed) ? 'checked' : '' }}>
                                    <label class="form-check-label">تمت التسوية النهائية</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">مبلغ التسوية النهائية</label>
                                <input type="number" name="final_settlement_amount" class="form-control" 
                                       value="{{ old('final_settlement_amount', $exit->final_settlement_amount) }}" step="0.01">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ التسوية النهائية</label>
                                <input type="date" name="final_settlement_date" class="form-control" 
                                       value="{{ old('final_settlement_date', $exit->final_settlement_date?->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">السبب</label>
                                <textarea name="reason" class="form-control" rows="3">{{ old('reason', $exit->reason) }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $exit->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.employee-exits.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
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


