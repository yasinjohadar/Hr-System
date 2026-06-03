@extends('admin.layouts.master')

@section('page-title')
    تعديل رئيس قسم
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
            <div class="page-header my-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="page-title mb-1">تعديل أقسام: {{ $head->name }}</h5>
                    <p class="text-muted small mb-0">{{ $head->email }}</p>
                </div>
                <a href="{{ route('admin.department-heads.show', $head->id) }}" class="btn btn-outline-secondary btn-sm">رجوع</a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.department-heads.update', $head->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label">الأقسام المُدارة</label>
                            <div class="border rounded p-3" style="max-height: 360px; overflow-y: auto;">
                                @php
                                    $selected = old('department_ids', $managedDepartmentIds);
                                @endphp
                                @foreach ($departments as $department)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="department_ids[]"
                                               value="{{ $department->id }}" id="dept_{{ $department->id }}"
                                               @checked(in_array($department->id, $selected))>
                                        <label class="form-check-label" for="dept_{{ $department->id }}">
                                            {{ $department->name }}
                                            @if ($department->manager && (int) $department->manager_id !== (int) $head->id)
                                                <span class="badge bg-warning-transparent text-warning ms-1">
                                                    المدير الحالي: {{ $department->manager->name }}
                                                </span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <small class="text-muted">إلغاء تحديد جميع الأقسام يزيل التعيين وقد يُلغى دور رئيس القسم.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
