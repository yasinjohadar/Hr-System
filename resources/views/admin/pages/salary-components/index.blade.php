@extends('admin.layouts.master')

@section('page-title')
    مكونات الراتب
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
                    <h5 class="page-title fs-21 mb-1">مكونات الراتب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('salary-component-create')
                            <a href="{{ route('admin.salary-components.create') }}" class="btn btn-primary btn-sm">إضافة مكون جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.salary-components.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="type" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        <option value="allowance" {{ request('type') == 'allowance' ? 'selected' : '' }}>بدل</option>
                                        <option value="deduction" {{ request('type') == 'deduction' ? 'selected' : '' }}>خصم</option>
                                        <option value="bonus" {{ request('type') == 'bonus' ? 'selected' : '' }}>مكافأة</option>
                                        <option value="overtime" {{ request('type') == 'overtime' ? 'selected' : '' }}>ساعات إضافية</option>
                                    </select>

                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.salary-components.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
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
                                            <th>النوع</th>
                                            <th>طريقة الحساب</th>
                                            <th>القيمة الافتراضية</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($components as $component)
                                            <tr>
                                                <td>{{ $component->code }}</td>
                                                <td>{{ $component->name_ar ?? $component->name }}</td>
                                                <td>
                                                    <span class="badge bg-{{ match($component->type) {
                                                        'allowance' => 'success',
                                                        'deduction' => 'danger',
                                                        'bonus' => 'info',
                                                        'overtime' => 'warning',
                                                        default => 'secondary'
                                                    } }}">
                                                        {{ $component->type_name_ar }}
                                                    </span>
                                                </td>
                                                <td>{{ $component->calculation_type_name_ar }}</td>
                                                <td>
                                                    @if($component->calculation_type == 'percentage')
                                                        {{ $component->percentage }}%
                                                    @else
                                                        {{ number_format($component->default_value, 2) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $component->is_active ? 'success' : 'secondary' }}">
                                                        {{ $component->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('salary-component-show')
                                                        <a href="{{ route('admin.salary-components.show', $component->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('salary-component-edit')
                                                        <a href="{{ route('admin.salary-components.edit', $component->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('salary-component-delete')
                                                        <form action="{{ route('admin.salary-components.destroy', $component->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                                <td colspan="7" class="text-center">لا توجد مكونات</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $components->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

