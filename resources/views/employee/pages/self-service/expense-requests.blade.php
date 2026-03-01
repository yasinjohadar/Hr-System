@extends('employee.layouts.master')

@section('page-title')
    طلبات المصروفات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">طلبات المصروفات</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة طلبات المصروفات ({{ $expenseRequests->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>التصنيف</th>
                                    <th>العنوان</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expenseRequests as $request)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $request->request_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->category->name_ar ?? $request->category->name }}</td>
                                        <td>{{ $request->title }}</td>
                                        <td>
                                            {{ number_format($request->amount, 2) }} 
                                            {{ $request->currency->symbol ?? $request->currency->code ?? '' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : ($request->status == 'paid' ? 'info' : 'warning')) }}">
                                                {{ $request->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $request->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal للتفاصيل -->
                                    <div class="modal fade" id="viewModal{{ $request->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تفاصيل طلب المصروف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>التاريخ:</strong></div>
                                                        <div class="col-md-6">{{ $request->request_date->format('Y-m-d') }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>التصنيف:</strong></div>
                                                        <div class="col-md-6">{{ $request->category->name_ar ?? $request->category->name }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>العنوان:</strong></div>
                                                        <div class="col-md-6">{{ $request->title }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>المبلغ:</strong></div>
                                                        <div class="col-md-6">
                                                            {{ number_format($request->amount, 2) }} 
                                                            {{ $request->currency->symbol ?? $request->currency->code ?? '' }}
                                                        </div>
                                                    </div>
                                                    @if($request->description)
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>الوصف:</strong></div>
                                                        <div class="col-md-6">{{ $request->description }}</div>
                                                    </div>
                                                    @endif
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>الحالة:</strong></div>
                                                        <div class="col-md-6">
                                                            <span class="badge bg-{{ $request->status == 'approved' ? 'success' : ($request->status == 'rejected' ? 'danger' : ($request->status == 'paid' ? 'info' : 'warning')) }}">
                                                                {{ $request->status_name_ar }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @if($request->rejection_reason)
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>سبب الرفض:</strong></div>
                                                        <div class="col-md-6 text-danger">{{ $request->rejection_reason }}</div>
                                                    </div>
                                                    @endif
                                                    @if($request->receipt_path)
                                                    <div class="row mb-3">
                                                        <div class="col-md-6"><strong>الإيصال:</strong></div>
                                                        <div class="col-md-6">
                                                            <a href="{{ asset('storage/' . $request->receipt_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-download"></i> تحميل
                                                            </a>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد طلبات مصروفات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $expenseRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

