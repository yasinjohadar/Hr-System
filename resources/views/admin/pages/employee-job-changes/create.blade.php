@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب تغيير وظيفي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة طلب تغيير وظيفي</h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.employee-job-changes.index') }}" class="btn btn-secondary btn-sm">عودة</a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">بيانات الطلب</h5>
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

                            <form action="{{ route('admin.employee-job-changes.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">الموظف <span class="text-danger">*</span></label>
                                    <select name="employee_id" id="employee_id" class="form-select" required onchange="loadEmployeeData()">
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $emp)
                                            <option value="{{ $emp->id }}" data-department="{{ $emp->department_id }}" data-position="{{ $emp->position_id }}" data-branch="{{ $emp->branch_id }}" data-salary="{{ $emp->salary }}">
                                                {{ $emp->full_name }} ({{ $emp->employee_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">نوع التغيير <span class="text-danger">*</span></label>
                                            <select name="change_type" id="change_type" class="form-select" required onchange="toggleFields()">
                                                <option value="">اختر نوع التغيير</option>
                                                <option value="transfer">نقل</option>
                                                <option value="promotion">ترقية</option>
                                                <option value="salary_change">تعديل راتب</option>
                                                <option value="demotion">تنزيل</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">التاريخ الفعال <span class="text-danger">*</span></label>
                                            <input type="date" name="effective_date" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- القيم الحالية (للعرض فقط) -->
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">القيم الحالية للموظف:</h6>
                                    <div id="currentValues" class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>القسم:</strong> <span id="current_department">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>المنصب:</strong> <span id="current_position">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>الفرع:</strong> <span id="current_branch">-</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>الراتب:</strong> <span id="current_salary">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول النقل -->
                                <div id="transfer_fields" class="change-type-fields" style="display: none;">
                                    <h6 class="mb-3">بيانات النقل:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">القسم الجديد</label>
                                                <select name="new_department_id" class="form-select">
                                                    <option value="">بدون تغيير</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
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
                                                        <option value="{{ $pos->id }}">{{ $pos->title }}</option>
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
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                                                        <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول الترقية -->
                                <div id="promotion_fields" class="change-type-fields" style="display: none;">
                                    <h6 class="mb-3">بيانات الترقية:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المنصب الجديد</label>
                                                <select name="new_position_id" class="form-select">
                                                    <option value="">اختر المنصب</option>
                                                    @foreach ($positions as $pos)
                                                        <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">الراتب الجديد</label>
                                                <input type="number" name="new_salary" class="form-control" step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- حقول تعديل الراتب -->
                                <div id="salary_change_fields" class="change-type-fields" style="display: none;">
                                    <h6 class="mb-3">بيانات تعديل الراتب:</h6>
                                    <div class="mb-3">
                                        <label class="form-label">الراتب الجديد <span class="text-danger">*</span></label>
                                        <input type="number" name="new_salary" class="form-control" step="0.01" min="0" required>
                                    </div>
                                </div>

                                <!-- حقول التنزيل -->
                                <div id="demotion_fields" class="change-type-fields" style="display: none;">
                                    <h6 class="mb-3">بيانات التنزيل:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">المنصب الجديد</label>
                                                <select name="new_position_id" class="form-select">
                                                    <option value="">اختر المنصب</option>
                                                    @foreach ($positions as $pos)
                                                        <option value="{{ $pos->id }}">{{ $pos->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">الراتب الجديد</label>
                                                <input type="number" name="new_salary" class="form-control" step="0.01" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="reason" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">حفظ الطلب</button>
                                    <a href="{{ route('admin.employee-job-changes.index') }}" class="btn btn-secondary">إلغاء</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loadEmployeeData() {
            const select = document.getElementById('employee_id');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption.value) {
                document.getElementById('current_department').textContent = selectedOption.dataset.department || '-';
                document.getElementById('current_position').textContent = selectedOption.dataset.position || '-';
                document.getElementById('current_branch').textContent = selectedOption.dataset.branch || '-';
                document.getElementById('current_salary').textContent = selectedOption.dataset.salary ? selectedOption.dataset.salary + ' ريال' : '-';
            } else {
                document.getElementById('current_department').textContent = '-';
                document.getElementById('current_position').textContent = '-';
                document.getElementById('current_branch').textContent = '-';
                document.getElementById('current_salary').textContent = '-';
            }
        }

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
