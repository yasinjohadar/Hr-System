@extends('admin.layouts.master')

@section('page-title')
    إدارة التفويض
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">إدارة التفويض</h5>
                    <p class="text-muted fs-13 mb-0">تفويض صلاحيات الموافقة</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.team.delegations.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i>تفويض جديد
                    </a>
                    <a href="{{ route('admin.team.dashboard') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ri-arrow-left-line me-1"></i>العودة
                    </a>
                </div>
            </div>

            <!-- Active Delegations -->
            <div class="card custom-card mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title fw-semibold mb-0">
                        <i class="ri-share-line me-1"></i>التفويضات الصادرة
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المفوَّض</th>
                                    <th>أنواع الطلبات</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>الحالة</th>
                                    <th>ملاحظات</th>
                                    <th>إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($delegations as $delegation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary-transparent avatar-rounded me-2">
                                                    {{ substr($delegation->delegate->name, 0, 1) }}
                                                </div>
                                                <span class="fw-medium">{{ $delegation->delegate->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if(empty($delegation->workflow_types))
                                                <span class="badge bg-info-transparent">جميع الأنواع</span>
                                            @else
                                                @foreach($delegation->workflow_types as $type)
                                                    <span class="badge bg-primary-transparent me-1">{{ $type }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $delegation->start_date->format('Y/m/d H:i') }}</td>
                                        <td>{{ $delegation->end_date->format('Y/m/d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $delegation->status === 'active' ? 'success' : ($delegation->status === 'expired' ? 'danger' : 'warning') }}-transparent">
                                                {{ $delegation->status_name_ar }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($delegation->notes, 50) ?? '-' }}</td>
                                        <td>
                                            @if($delegation->status === 'active')
                                                <form action="{{ route('admin.team.delegations.cancel', $delegation->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذا التفويض؟')">
                                                        <i class="ri-close-line"></i> إلغاء
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            لا توجد تفويضات صادرة
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Received Delegations -->
            <div class="card custom-card">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title fw-semibold mb-0">
                        <i class="ri-arrow-down-circle-line me-1"></i>التفويضات المستلمة
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>المفوِّض</th>
                                    <th>أنواع الطلبات</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($receivedDelegations as $delegation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-success-transparent avatar-rounded me-2">
                                                    {{ substr($delegation->delegator->name, 0, 1) }}
                                                </div>
                                                <span class="fw-medium">{{ $delegation->delegator->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if(empty($delegation->workflow_types))
                                                <span class="badge bg-info-transparent">جميع الأنواع</span>
                                            @else
                                                @foreach($delegation->workflow_types as $type)
                                                    <span class="badge bg-success-transparent me-1">{{ $type }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{ $delegation->start_date->format('Y/m/d H:i') }}</td>
                                        <td>{{ $delegation->end_date->format('Y/m/d H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $delegation->status === 'active' ? 'success' : ($delegation->status === 'expired' ? 'danger' : 'warning') }}-transparent">
                                                {{ $delegation->status_name_ar }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            لا توجد تفويضات مستلمة
                                        </td>
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
