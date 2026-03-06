@extends('admin.layouts.master')

@section('page-title')
    إعلانات الشركة
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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
                    <h5 class="page-title fs-21 mb-1">إعلانات الشركة</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">إضافة إعلان جديد</a>
                            <form action="{{ route('admin.announcements.index') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                                <input style="width: 220px" type="text" name="search" class="form-control"
                                    placeholder="بحث بالعنوان أو المحتوى" value="{{ request('search') }}">
                                <select name="status" class="form-select" style="width: 140px">
                                    <option value="">كل الحالات</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشف</option>
                                </select>
                                <button type="submit" class="btn btn-secondary">بحث</button>
                                <a href="{{ route('admin.announcements.index') }}" class="btn btn-danger">مسح</a>
                            </form>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>العنوان</th>
                                            <th>الحالة</th>
                                            <th>الاستهداف</th>
                                            <th>تاريخ النشر</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($announcements as $announcement)
                                            <tr>
                                                <th>{{ $loop->iteration + ($announcements->currentPage() - 1) * $announcements->perPage() }}</th>
                                                <td>
                                                    <strong>{{ $announcement->title }}</strong>
                                                    @if ($announcement->content)
                                                        <br><small class="text-muted">{{ Str::limit(strip_tags($announcement->content), 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($announcement->status === 'published')
                                                        <span class="badge bg-success">{{ $announcement->status_label }}</span>
                                                    @elseif($announcement->status === 'draft')
                                                        <span class="badge bg-secondary">{{ $announcement->status_label }}</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">{{ $announcement->status_label }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $announcement->target_type_label }} @if($announcement->department) ({{ $announcement->department->name_ar ?? $announcement->department->name }}) @endif @if($announcement->branch) ({{ $announcement->branch->name_ar ?? $announcement->branch->name }}) @endif</td>
                                                <td>{{ $announcement->publish_date?->format('Y-m-d') ?? '—' }}</td>
                                                <td>{{ $announcement->expiry_date?->format('Y-m-d') ?? '—' }}</td>
                                                <td>
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.announcements.show', $announcement) }}" title="عرض"><i class="fa-solid fa-eye"></i></a>
                                                    <a class="btn btn-warning btn-sm me-1" href="{{ route('admin.announcements.edit', $announcement) }}" title="تعديل"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    <a class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#delete{{ $announcement->id }}" title="حذف"><i class="fa-solid fa-trash-can"></i></a>
                                                </td>
                                            </tr>
                                            @include('admin.pages.announcements.delete')
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">لا توجد إعلانات.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                @if ($announcements->hasPages())
                                    <div class="mt-3">{{ $announcements->withQueryString()->links() }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop
