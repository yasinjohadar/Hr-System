@extends('employee.layouts.master')

@section('page-title')
    المزايا والتعويضات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">المزايا والتعويضات</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المزايا ({{ $benefits->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>نوع الميزة</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>القيمة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($benefits as $benefit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $benefit->benefitType->name_ar ?? $benefit->benefitType->name }}</td>
                                        <td>{{ $benefit->start_date ? $benefit->start_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $benefit->end_date ? $benefit->end_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $benefit->value }}</td>
                                        <td>
                                            <span class="badge bg-{{ $benefit->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $benefit->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا توجد مزايا</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

