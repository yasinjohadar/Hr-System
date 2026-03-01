@extends('admin.layouts.master')

@section('page-title')
    أنواع المكافآت
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">أنواع المكافآت</h5>
                </div>
                <div>
                    @can('reward-type-create')
                    <a href="{{ route('admin.reward-types.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة نوع جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reward-types.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="monetary" {{ request('type') == 'monetary' ? 'selected' : '' }}>نقدي</option>
                                <option value="non_monetary" {{ request('type') == 'non_monetary' ? 'selected' : '' }}>غير نقدي</option>
                                <option value="points" {{ request('type') == 'points' ? 'selected' : '' }}>نقاط</option>
                                <option value="recognition" {{ request('type') == 'recognition' ? 'selected' : '' }}>اعتراف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="is_active" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة أنواع المكافآت ({{ $rewardTypes->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>الكود</th>
                                    <th>النوع</th>
                                    <th>القيمة الافتراضية</th>
                                    <th>عدد المكافآت</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rewardTypes as $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $type->name_ar ?? $type->name }}</strong></td>
                                        <td>{{ $type->code ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $type->type_name_ar }}</span></td>
                                        <td>
                                            @if($type->type == 'monetary')
                                                {{ number_format($type->default_value, 2) }} ر.س
                                            @elseif($type->type == 'points')
                                                {{ $type->default_points }} نقطة
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $type->employee_rewards_count }}</td>
                                        <td>
                                            <span class="badge bg-{{ $type->is_active ? 'success' : 'danger' }}">
                                                {{ $type->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('reward-type-show')
                                            <a href="{{ route('admin.reward-types.show', $type->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('reward-type-edit')
                                            <a href="{{ route('admin.reward-types.edit', $type->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد أنواع مكافآت</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $rewardTypes->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

