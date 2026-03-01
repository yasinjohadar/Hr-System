@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المهارة
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">تفاصيل المهارة</h5>
                </div>
                <div>
                    <a href="{{ route('admin.employee-skills.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $skill->skill_name_ar ?? $skill->skill_name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">الموظف:</label>
                                    <p class="form-control-plaintext">{{ $skill->employee->full_name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">مستوى الكفاءة:</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $skill->proficiency_level == 'expert' ? 'success' : ($skill->proficiency_level == 'advanced' ? 'info' : ($skill->proficiency_level == 'intermediate' ? 'warning' : 'secondary')) }}">
                                            {{ $skill->proficiency_level_name_ar }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">سنوات الخبرة:</label>
                                    <p class="form-control-plaintext">{{ $skill->years_of_experience ?? '-' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">تم التحقق:</label>
                                    <p class="form-control-plaintext">
                                        @if ($skill->is_verified)
                                            <span class="badge bg-success">نعم</span>
                                            @if ($skill->verifier)
                                                <small class="text-muted">بواسطة: {{ $skill->verifier->name }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-warning">لا</span>
                                        @endif
                                    </p>
                                </div>
                                @if ($skill->description)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">الوصف:</label>
                                    <p class="form-control-plaintext">{{ $skill->description }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                @can('employee-skill-edit')
                                <a href="{{ route('admin.employee-skills.edit', $skill->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>تعديل
                                </a>
                                @endcan
                                @if (!$skill->is_verified)
                                <a href="{{ route('admin.employee-skills.verify', $skill->id) }}" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>التحقق من المهارة
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


