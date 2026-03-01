@extends('admin.layouts.master')

@section('page-title')
    إضافة قاعدة حضور جديدة
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
                <h5 class="page-title mb-0">إضافة قاعدة حضور جديدة</h5>
                <a href="{{ route('admin.attendance-rules.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.attendance-rules.store') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الاسم بالعربية</label>
                                <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع القاعدة <span class="text-danger">*</span></label>
                                <select class="form-select @error('rule_type') is-invalid @enderror" name="rule_type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="late" {{ old('rule_type') == 'late' ? 'selected' : '' }}>تأخير</option>
                                    <option value="absent" {{ old('rule_type') == 'absent' ? 'selected' : '' }}>غياب</option>
                                    <option value="early_leave" {{ old('rule_type') == 'early_leave' ? 'selected' : '' }}>انصراف مبكر</option>
                                    <option value="overtime" {{ old('rule_type') == 'overtime' ? 'selected' : '' }}>ساعات إضافية</option>
                                    <option value="break" {{ old('rule_type') == 'break' ? 'selected' : '' }}>استراحة</option>
                                    <option value="holiday" {{ old('rule_type') == 'holiday' ? 'selected' : '' }}>عطلة</option>
                                </select>
                                @error('rule_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الحد الأدنى (بالدقائق) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('threshold_minutes') is-invalid @enderror" 
                                       name="threshold_minutes" value="{{ old('threshold_minutes', 0) }}" min="0" required>
                                @error('threshold_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">نوع الإجراء <span class="text-danger">*</span></label>
                                <select class="form-select @error('action_type') is-invalid @enderror" name="action_type" required id="action_type">
                                    <option value="">اختر نوع الإجراء</option>
                                    <option value="warning" {{ old('action_type') == 'warning' ? 'selected' : '' }}>تحذير</option>
                                    <option value="deduction" {{ old('action_type') == 'deduction' ? 'selected' : '' }}>خصم</option>
                                    <option value="notification" {{ old('action_type') == 'notification' ? 'selected' : '' }}>إشعار</option>
                                    <option value="block" {{ old('action_type') == 'block' ? 'selected' : '' }}>حظر</option>
                                </select>
                                @error('action_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="deduction_amount_div" style="display: none;">
                                <label class="form-label">مبلغ الخصم</label>
                                <input type="number" step="0.01" class="form-control" name="deduction_amount" value="{{ old('deduction_amount') }}" min="0">
                            </div>

                            <div class="col-md-6" id="deduction_percentage_div" style="display: none;">
                                <label class="form-label">نسبة الخصم (%)</label>
                                <input type="number" class="form-control" name="deduction_percentage" value="{{ old('deduction_percentage') }}" min="0" max="100">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الأولوية</label>
                                <input type="number" class="form-control" name="priority" value="{{ old('priority', 0) }}" min="0">
                                <small class="text-muted">كلما زاد الرقم زادت الأولوية</small>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="apply_to_all" value="1" id="apply_to_all" {{ old('apply_to_all') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apply_to_all">يطبق على جميع الموظفين</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="send_notification" value="1" id="send_notification" {{ old('send_notification', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="send_notification">إرسال إشعار</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">نشط</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                            <a href="{{ route('admin.attendance-rules.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('js')
    <script>
        document.getElementById('action_type').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('deduction_amount_div').style.display = type === 'deduction' ? 'block' : 'none';
            document.getElementById('deduction_percentage_div').style.display = type === 'deduction' ? 'block' : 'none';
        });
        document.getElementById('action_type').dispatchEvent(new Event('change'));
    </script>
    @stop
@stop

