@extends('admin.layouts.master')

@section('page-title')
    دليل الموظفين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">دليل الموظفين</h5>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" onclick="exportDirectory()">
                        <i class="fas fa-download me-2"></i>تصدير
                    </button>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.employee-directory.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="بحث (اسم، كود، بريد، هاتف)..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="department_id" class="form-select">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="branch_id" class="form-select">
                                <option value="">كل الفروع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="position_id" class="form-select">
                                <option value="">كل المناصب</option>
                                @foreach ($positions as $pos)
                                    <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                                        {{ $pos->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- دليل الموظفين -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة الموظفين ({{ $employees->total() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($employees as $employee)
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            @if ($employee->photo)
                                                <img src="{{ asset('storage/' . $employee->photo) }}" 
                                                     alt="{{ $employee->full_name }}" 
                                                     class="rounded-circle" 
                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 100px; height: 100px;">
                                                    <i class="fas fa-user fa-3x text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <h5 class="card-title mb-1">
                                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="text-decoration-none">
                                                {{ $employee->full_name }}
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-1 small">
                                            <strong>الكود:</strong> {{ $employee->employee_code }}
                                        </p>
                                        @if ($employee->position)
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-briefcase me-1"></i>{{ $employee->position->title }}
                                        </p>
                                        @endif
                                        @if ($employee->department)
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-building me-1"></i>{{ $employee->department->name }}
                                        </p>
                                        @endif
                                        @if ($employee->branch)
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $employee->branch->name }}
                                        </p>
                                        @endif
                                        @if ($employee->email)
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-envelope me-1"></i>
                                            <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                                {{ $employee->email }}
                                            </a>
                                        </p>
                                        @endif
                                        @if ($employee->phone)
                                        <p class="text-muted mb-1 small">
                                            <i class="fas fa-phone me-1"></i>
                                            <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                                {{ $employee->phone }}
                                            </a>
                                        </p>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>عرض التفاصيل
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>لا توجد نتائج
                                </div>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-4">
                        {{ $employees->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function exportDirectory() {
        const params = new URLSearchParams(window.location.search);
        params.append('format', 'pdf');
        
        window.open('{{ route("admin.employee-directory.export") }}?' + params.toString(), '_blank');
    }
</script>
@stop

