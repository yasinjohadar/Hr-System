@extends('employee.layouts.master')

@section('page-title')
    التذاكر
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التذاكر</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة التذاكر ({{ $tickets->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود التذكرة</th>
                                    <th>الموضوع</th>
                                    <th>الفئة</th>
                                    <th>الأولوية</th>
                                    <th>المكلف</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ticket->ticket_code }}</td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td><span class="badge bg-info">{{ $ticket->category_name_ar }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                                {{ $ticket->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->assignedTo->full_name ?? 'غير مكلف' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : ($ticket->status == 'in_progress' ? 'primary' : 'warning') }}">
                                                {{ $ticket->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد تذاكر</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

