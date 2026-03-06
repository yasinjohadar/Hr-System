@extends('admin.layouts.master')

@section('page-title')
    السياسات واللوائح
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
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
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">السياسات واللوائح</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3 flex-wrap">
                            <a href="{{ route('admin.policies.create') }}" class="btn btn-primary btn-sm">إضافة سياسة جديدة</a>
                            <form action="{{ route('admin.policies.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <input type="text" name="search" class="form-control" style="width: 200px"
                                    placeholder="بحث بالعنوان أو المحتوى" value="{{ request('search') }}">
                                <select name="is_active" class="form-select" style="width: 140px">
                                    <option value="">كل الحالات</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                                <input type="text" name="category" class="form-control" style="width: 150px"
                                    placeholder="التصنيف" value="{{ request('category') }}">
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.policies.index') }}" class="btn btn-danger">مسح</a>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>العنوان</th>
                                            <th>التصنيف</th>
                                            <th>الإصدار</th>
                                            <th>تاريخ السريان</th>
                                            <th>الحالة</th>
                                            <th>عدد الاعترافات</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($policies as $policy)
                                            <tr>
                                                <th>{{ $loop->iteration + ($policies->currentPage() - 1) * $policies->perPage() }}</th>
                                                <td>
                                                    <strong>{{ $policy->title }}</strong>
                                                    @if ($policy->content)
                                                        <br><small class="text-muted">{{ Str::limit(strip_tags($policy->content), 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $policy->category ?? '—' }}</td>
                                                <td>{{ $policy->version ?? '—' }}</td>
                                                <td>{{ $policy->effective_date?->format('Y-m-d') ?? '—' }}</td>
                                                <td>
                                                    @if ($policy->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-secondary">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>{{ $policy->acknowledgments->count() }}</td>
                                                <td>
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.policies.show', $policy) }}" title="عرض"><i class="fa fa-eye"></i></a>
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.policies.edit', $policy) }}" title="تعديل"><i class="fa fa-edit"></i></a>
                                                    <form action="{{ route('admin.policies.destroy', $policy) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه السياسة؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="حذف"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">لا توجد سياسات.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if ($policies->hasPages())
                                <div class="mt-3">{{ $policies->withQueryString()->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
