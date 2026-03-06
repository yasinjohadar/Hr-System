@extends('employee.layouts.master')

@section('page-title')
    سجل التدريب
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">سجل التدريب</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">سجلات التدريب ({{ $records->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الدورة / البرنامج</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>تاريخ الإكمال</th>
                                    <th>الحالة</th>
                                    <th>النتيجة</th>
                                    <th>شهادة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $record)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $record->training->title_ar ?? $record->training->title ?? '-' }}</td>
                                        <td>{{ $record->registration_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $record->completion_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $record->status_ar }}</span></td>
                                        <td>{{ $record->score !== null ? number_format($record->score, 1) : '-' }} {{ $record->score !== null ? '(' . $record->score_rating . ')' : '' }}</td>
                                        <td>{{ $record->certificate_issued ? 'نعم' : 'لا' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">لا توجد سجلات تدريب</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($records->hasPages())
                        <div class="mt-3">{{ $records->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
