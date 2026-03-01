@extends('admin.layouts.master')

@section('page-title')
    نظام التذاكر
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">نظام التذاكر</h5>
                </div>
                <div>
                    @can('ticket-create')
                    <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>إنشاء تذكرة جديدة
                    </a>
                    @endcan
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.tickets.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>محلول</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="priority" class="form-select">
                                <option value="">كل الأولويات</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجل</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">كل الفئات</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                <option value="hr" {{ request('category') == 'hr' ? 'selected' : '' }}>موارد بشرية</option>
                                <option value="it" {{ request('category') == 'it' ? 'selected' : '' }}>تقنية معلومات</option>
                                <option value="facilities" {{ request('category') == 'facilities' ? 'selected' : '' }}>مرافق</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق</button>
                        </div>
                    </form>
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
                                    <th>العنوان</th>
                                    <th>الفئة</th>
                                    <th>الأولوية</th>
                                    <th>الموظف</th>
                                    <th>المكلف</th>
                                    <th>عدد التعليقات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $ticket->ticket_code }}</td>
                                        <td>{{ Str::limit($ticket->title, 30) }}</td>
                                        <td><span class="badge bg-secondary">{{ $ticket->category_name_ar }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : 'info') }}">
                                                {{ $ticket->priority_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->employee->full_name ?? '-' }}</td>
                                        <td>{{ $ticket->assignedTo->full_name ?? 'غير مكلف' }}</td>
                                        <td>{{ $ticket->comments_count }}</td>
                                        <td><span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : ($ticket->status == 'closed' ? 'secondary' : 'primary') }}">{{ $ticket->status_name_ar }}</span></td>
                                        <td>
                                            @can('ticket-show')
                                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">لا توجد تذاكر</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $tickets->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

