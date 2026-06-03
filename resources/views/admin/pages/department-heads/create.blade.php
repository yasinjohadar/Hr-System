@extends('admin.layouts.master')

@section('page-title')
    تعيين رئيس قسم
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header my-4">
                <h5 class="page-title mb-1">تعيين رئيس قسم جديد</h5>
                <a href="{{ route('admin.department-heads.index') }}" class="btn btn-outline-secondary btn-sm">رجوع للقائمة</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.department-heads.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label">المستخدم <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">— اختر مستخدماً —</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                        {{ $user->name }} ({{ $user->email }})
                                        @if ($user->employee)
                                            — {{ $user->employee->full_name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">لا يمكن اختيار مدير النظام (admin).</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">الأقسام المُدارة <span class="text-danger">*</span></label>
                            <div class="border rounded p-3" style="max-height: 320px; overflow-y: auto;">
                                @foreach ($departments as $department)
                                    @php
                                        $hasOtherManager = $department->manager_id && ! in_array($department->id, (array) old('department_ids', []));
                                    @endphp
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="department_ids[]"
                                               value="{{ $department->id }}" id="dept_{{ $department->id }}"
                                               @checked(in_array($department->id, (array) old('department_ids', [])))>
                                        <label class="form-check-label" for="dept_{{ $department->id }}">
                                            {{ $department->name }}
                                            @if ($department->code)
                                                <span class="text-muted">({{ $department->code }})</span>
                                            @endif
                                            @if ($department->manager && $department->manager_id != old('user_id'))
                                                <span class="badge bg-warning-transparent text-warning ms-1">
                                                    المدير الحالي: {{ $department->manager->name }}
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('department_ids')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">سيُستبدل المدير الحالي للقسم إن وُجد.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">حفظ التعيين</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
