@extends('admin.layouts.master')

@section('page-title')
    تعديل طلب تغيير وظيفي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تعديل طلب تغيير وظيفي</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.employee-job-changes.show', $employeeJobChange) }}" class="btn btn-secondary btn-sm">عودة</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">تعديل بيانات الطلب</h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.employee-job-changes.update', $employeeJobChange) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">الموظف</label>
                                            <input type="text" class="form-control" value="{{ $employeeJobChange->employee->full_name }}" disabled>
                                            <input type="hidden" name="employee_id" value="{{ $employeeJobChange->employee_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">نوع التغيير <span class="text-danger">*</span></label>
                                            <select name="change_type" id="change_type" class="form-select" required onchange="toggleFields()">
                                                <option value="transfer" {{ $employeeJobChange->change_type == 'transfer' ? 'selected' : '' }}>نقل</option>
                                                <option value="promotion" {{ $employeeJobChange->change_type == 'promotion' ? 'selected' : '' }}>ترقية</option>
                                                <option value="salary_change" {{ $employeeJobChange->change_type == 'salary_change' ? 'selected' : '' }}>تعديل راتب</option>
                                                <option value="demotion" {{ $employeeJobChange->change_type == 'demotion' ? 'selected' : '' }}>تنزيل</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">التاريخ الفعال <span class="text-danger">*</span></label>
                                            <input type="date" name="effective_date" class="form-control" value="{{ $employeeJobChange->effective_date->format('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- القيم الحالية (للعرض فقط) -->
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">القيم الحالية للموظف:</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>القسم:</strong> {{ $employeeJobChange->oldDepartment->name ?? '-' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>المنصب:</strong> {{ $employeeJobChange->oldPosition->title ?? '-' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>الفرع:</strong> {{ $employeeJobChange->oldBranch->name ?? '-' }}
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>الراتب:</strong> {{ $employeeJobChange->old_salary ? number_format($employeeJobChange->old_salary, 2) . ' ريال' : '-' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول النقل -->
                                <div id="transfer_fields" class="change-type-fields" style="display: {{ $employeeJobChange->change_type == 'transfer' ? 'block' : 'none' }};">
                                    <h6 class="mb-3">بيانات النقل:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">القسم الجديد</label>
                                                <select name="new_department_id" class="form-select">
                                                    <option value="">بدون تغيير</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}" {{ $employeeJobChange->new_department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المنصب الجديد</label>
                                                <select name="new_position_id" class="form-select">
                                                    <option value="">بدون تغيير</option>
                                                    @foreach ($positions as $pos)
                                                        <option value="{{ $pos->id }}" {{ $employeeJobChange->new_position_id == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">الفرع الجديد</label>
                                                <select name="new_branch_id" class="form-select">
                                                    <option value="">بدون تغيير</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}" {{ $employeeJobChange->new_branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المدير الجديد</label>
                                                <select name="new_manager_id" class="form-select">
                                                    <option value="">بدون تغيير</option>
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->id }}" {{ $employeeJobChange->new_manager_id == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول الترقية -->
                                <div id="promotion_fields" class="change-type-fields" style="display: {{ $employeeJobChange->change_type == 'promotion' ? 'block' : 'none' }};">
                                    <h6 class="mb-3">بيانات الترقية:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المنصب الجديد</label>
                                                <select name="new_position_id" class="form-select">
                                                    <option value="">اختر المنصب</option>
                                                    @foreach ($positions as $pos)
                                                        <option value="{{ $pos->id }}" {{ $employeeJobChange->new_position_id == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">الراتب الجديد</label>
                                                <input type="number" name="new_salary" class="form-control" step="0.01" min="0" value="{{ $employeeJobChange->new_salary }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول تعديل الراتب -->
                                <div id="salary_change_fields" class="change-type-fields" style="display: {{ $employeeJobChange->change_type == 'salary_change' ? 'block' : 'none' }};">
                                    <h6 class="mb-3">بيانات تعديل الراتب:</h6>
                                    <div class="mb-3">
                                        <label class="form-label">الراتب الجديد <span class="text-danger">*</span></label>
                                        <input type="number" name="new_salary" class="form-control" step="0.01" min="0" value="{{ $employeeJobChange->new_salary }}" required>
                                    </div>
                                </div>

                                <!-- حقول التنزيل -->
                                <div id="demotion_fields" class="change-type-fields" style="display: {{ $employeeJobChange->change_type == 'demotion' ? 'block' : 'none' }};">
                                    <h6 class="mb-3">بيانات التنزيل:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المنصب الجديد</label>
                                                <select name="new_position_id" class="form-select">
                                                    <option value="">اختر المنصب</option>
                                                    @foreach ($positions as $pos)
                                                        <option value="{{ $pos->id }}" {{ $employeeJobChange->new_position_id == $pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">الراتب الجديد</label>
                                                <input type="number" name="new_salary" class="form-control" step="0.01" min="0" value="{{ $employeeJobChange->new_salary }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="reason" class="form-control" rows="3">{{ $employeeJobChange->reason }}</textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                    <a href="{{ route('admin.employee-job-changes.show', $employeeJobChange) }}" class="btn btn-secondary">إلغاء</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleFields() {
            const changeType = document.getElementById('change_type').value;
            const fields = document.querySelectorAll('.change-type-fields');

            fields.forEach(field => {
                field.style.display = 'none';
            });

            if (changeType) {
                const targetField = document.getElementById(changeType + '_fields');
                if (targetField) {
                    targetField.style.display = 'block';
                }
            }
        }
    </script>
@endsection
