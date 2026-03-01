@extends('employee.layouts.master')

@section('page-title')
    الاجتماعات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الاجتماعات</h5>
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
                                    <th>عنوان الاجتماع</th>
                                    <th>المنظم</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>المكان</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($meetings as $meeting)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $meeting->title }}</td>
                                        <td>{{ $meeting->organizer->full_name ?? '-' }}</td>
                                        <td>{{ $meeting->start_time ? $meeting->start_time->format('Y-m-d H:i') : '-' }}</td>
                                        <td>{{ $meeting->end_time ? $meeting->end_time->format('Y-m-d H:i') : '-' }}</td>
                                        <td>{{ $meeting->location ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $meeting->status == 'completed' ? 'success' : ($meeting->status == 'scheduled' ? 'primary' : 'secondary') }}">
                                                {{ $meeting->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد اجتماعات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $meetings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

