@extends('admin.layouts.master')

@section('page-title')
    قواعد الحضور
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">قواعد الحضور</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('attendance-rule-create')
                            <a href="{{ route('admin.attendance-rules.create') }}" class="btn btn-primary btn-sm">إضافة قاعدة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.attendance-rules.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="rule_type" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        <option value="late" {{ request('rule_type') == 'late' ? 'selected' : '' }}>تأخير</option>
                                        <option value="absent" {{ request('rule_type') == 'absent' ? 'selected' : '' }}>غياب</option>
                                        <option value="early_leave" {{ request('rule_type') == 'early_leave' ? 'selected' : '' }}>انصراف مبكر</option>
                                        <option value="overtime" {{ request('rule_type') == 'overtime' ? 'selected' : '' }}>ساعات إضافية</option>
                                    </select>

                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.attendance-rules.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الكود</th>
                                            <th>الاسم</th>
                                            <th>نوع القاعدة</th>
                                            <th>الحد الأدنى (دقيقة)</th>
                                            <th>نوع الإجراء</th>
                                            <th>الأولوية</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($rules as $rule)
                                            <tr>
                                                <td>{{ $rule->rule_code }}</td>
                                                <td>{{ $rule->name_ar ?? $rule->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $rule->rule_type_name_ar }}</span>
                                                </td>
                                                <td>{{ $rule->threshold_minutes }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match($rule->action_type) {
                                                        'warning' => 'warning',
                                                        'deduction' => 'danger',
                                                        'notification' => 'info',
                                                        'block' => 'dark',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $rule->action_type_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $rule->priority }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}">
                                                        {{ $rule->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('attendance-rule-show')
                                                        <a href="{{ route('admin.attendance-rules.show', $rule->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('attendance-rule-edit')
                                                        <a href="{{ route('admin.attendance-rules.edit', $rule->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('attendance-rule-delete')
                                                        <form action="{{ route('admin.attendance-rules.destroy', $rule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">لا توجد قواعد</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $rules->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

