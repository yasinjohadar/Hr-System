@extends('employee.layouts.master')

@section('page-title')
    المستندات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">مستنداتي</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المستندات ({{ $documents->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نوع المستند</th>
                                    <th>العنوان</th>
                                    <th>تاريخ الإصدار</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><span class="badge bg-info">{{ $document->document_type_name_ar }}</span></td>
                                        <td>{{ $document->title }}</td>
                                        <td>{{ $document->issue_date ? $document->issue_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if ($document->expiry_date)
                                                <span class="{{ $document->is_expired ? 'text-danger' : '' }}">
                                                    {{ $document->expiry_date->format('Y-m-d') }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $document->status == 'active' ? 'success' : ($document->status == 'expired' ? 'danger' : 'warning') }}">
                                                {{ $document->status == 'active' ? 'نشط' : ($document->status == 'expired' ? 'منتهي' : 'قيد الانتظار') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد مستندات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


