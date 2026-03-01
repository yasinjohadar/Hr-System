@extends('admin.layouts.master')

@section('page-title')
    إعدادات الضرائب
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
                    <h5 class="page-title fs-21 mb-1">إعدادات الضرائب</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('tax-setting-create')
                            <a href="{{ route('admin.tax-settings.create') }}" class="btn btn-primary btn-sm">إضافة إعداد ضريبة جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.tax-settings.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="type" class="form-select" style="width: 150px;">
                                        <option value="">كل الأنواع</option>
                                        <option value="income_tax" {{ request('type') == 'income_tax' ? 'selected' : '' }}>ضريبة الدخل</option>
                                        <option value="social_insurance" {{ request('type') == 'social_insurance' ? 'selected' : '' }}>التأمينات الاجتماعية</option>
                                        <option value="health_insurance" {{ request('type') == 'health_insurance' ? 'selected' : '' }}>التأمين الصحي</option>
                                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>

                                    <select name="is_active" class="form-select" style="width: 120px;">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.tax-settings.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
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
                                            <th>النسبة/القيمة</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($taxSettings as $tax)
                                            <tr>
                                                <td>{{ $tax->code ?? '-' }}</td>
                                                <td>{{ $tax->name_ar ?? $tax->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $tax->type_name_ar }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($tax->calculation_method == 'percentage')
                                                        نسبة مئوية
                                                    @elseif($tax->calculation_method == 'slab')
                                                        شرائح
                                                    @else
                                                        ثابت
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($tax->calculation_method == 'percentage')
                                                        {{ $tax->rate }}%
                                                    @else
                                                        {{ number_format($tax->rate, 2) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }}">
                                                        {{ $tax->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('tax-setting-show')
                                                        <a href="{{ route('admin.tax-settings.show', $tax->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('tax-setting-edit')
                                                        <a href="{{ route('admin.tax-settings.edit', $tax->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('tax-setting-delete')
                                                        <form action="{{ route('admin.tax-settings.destroy', $tax->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
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
                                                <td colspan="7" class="text-center">لا توجد إعدادات ضرائب</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $taxSettings->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

