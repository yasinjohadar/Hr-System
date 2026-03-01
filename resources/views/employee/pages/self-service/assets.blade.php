@extends('employee.layouts.master')

@section('page-title')
    الأصول المعينة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الأصول المعينة</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الأصول المعينة ({{ $assets->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>كود الأصل</th>
                                    <th>اسم الأصل</th>
                                    <th>الفئة</th>
                                    <th>تاريخ التعيين</th>
                                    <th>تاريخ الإرجاع المتوقع</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assets as $assignment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $assignment->asset->asset_code ?? '-' }}</td>
                                        <td>{{ $assignment->asset->name_ar ?? $assignment->asset->name }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $assignment->asset->category_name_ar ?? $assignment->asset->category }}</span>
                                        </td>
                                        <td>{{ $assignment->assigned_date->format('Y-m-d') }}</td>
                                        <td>{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $assignment->assignment_status == 'active' ? 'success' : ($assignment->assignment_status == 'returned' ? 'secondary' : 'warning') }}">
                                                {{ $assignment->assignment_status_name_ar }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal{{ $assignment->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal للتفاصيل -->
                                    <div class="modal fade" id="viewModal{{ $assignment->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تفاصيل الأصل المعين</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>كود الأصل:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->asset_code ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>اسم الأصل:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->name_ar ?? $assignment->asset->name }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الفئة:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->category_name_ar ?? $assignment->asset->category }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>النوع:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->type ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الشركة المصنعة:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->manufacturer ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الموديل:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->model ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الرقم التسلسلي:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->asset->serial_number ?? '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>تاريخ التعيين:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->assigned_date->format('Y-m-d') }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>تاريخ الإرجاع المتوقع:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->expected_return_date ? $assignment->expected_return_date->format('Y-m-d') : '-' }}</div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الحالة:</strong></div>
                                                        <div class="col-md-8">
                                                            <span class="badge bg-{{ $assignment->assignment_status == 'active' ? 'success' : ($assignment->assignment_status == 'returned' ? 'secondary' : 'warning') }}">
                                                                {{ $assignment->assignment_status_name_ar }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    @if($assignment->condition_on_assignment)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>الحالة عند التعيين:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->condition_on_assignment_name_ar }}</div>
                                                    </div>
                                                    @endif
                                                    @if($assignment->assignment_notes)
                                                    <div class="row mb-3">
                                                        <div class="col-md-4"><strong>ملاحظات التعيين:</strong></div>
                                                        <div class="col-md-8">{{ $assignment->assignment_notes }}</div>
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
                                        <td colspan="8" class="text-center">لا توجد أصول معينة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

