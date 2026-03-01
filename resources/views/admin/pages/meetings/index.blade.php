@extends('admin.layouts.master')

@section('page-title')
    إدارة الاجتماعات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إدارة الاجتماعات</h5>
                </div>
                <div>
                    @can('meeting-create')
                    <a href="{{ route('admin.meetings.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إضافة اجتماع جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.meetings.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">كل الأنواع</option>
                                <option value="in_person" {{ request('type') == 'in_person' ? 'selected' : '' }}>حضوري</option>
                                <option value="virtual" {{ request('type') == 'virtual' ? 'selected' : '' }}>افتراضي</option>
                                <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>مختلط</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="من تاريخ">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الاجتماعات ({{ $meetings->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الاجتماع</th>
                                    <th>العنوان</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>النوع</th>
                                    <th>عدد الحضور</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($meetings as $meeting)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $meeting->meeting_code }}</td>
                                        <td>{{ $meeting->title_ar ?? $meeting->title }}</td>
                                        <td>{{ $meeting->start_time->format('Y-m-d H:i') }}</td>
                                        <td>{{ $meeting->end_time->format('Y-m-d H:i') }}</td>
                                        <td><span class="badge bg-info">{{ $meeting->type_name_ar }}</span></td>
                                        <td>{{ $meeting->attendees_count }}</td>
                                        <td><span class="badge bg-{{ $meeting->status == 'completed' ? 'success' : ($meeting->status == 'cancelled' ? 'danger' : 'primary') }}">{{ $meeting->status_name_ar }}</span></td>
                                        <td>
                                            @can('meeting-show')
                                            <a href="{{ route('admin.meetings.show', $meeting->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد اجتماعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $meetings->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

