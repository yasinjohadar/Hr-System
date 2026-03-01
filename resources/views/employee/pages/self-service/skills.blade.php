@extends('employee.layouts.master')

@section('page-title')
    المهارات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">مهاراتي</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">قائمة المهارات ({{ $skills->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($skills as $skill)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $skill->skill_name_ar ?? $skill->skill_name }}</h6>
                                        <p class="mb-2">
                                            <span class="badge bg-{{ $skill->proficiency_level == 'expert' ? 'success' : ($skill->proficiency_level == 'advanced' ? 'info' : ($skill->proficiency_level == 'intermediate' ? 'warning' : 'secondary')) }}">
                                                {{ $skill->proficiency_level_name_ar }}
                                            </span>
                                        </p>
                                        @if ($skill->years_of_experience)
                                            <p class="mb-1"><small class="text-muted">سنوات الخبرة: {{ $skill->years_of_experience }}</small></p>
                                        @endif
                                        @if ($skill->is_verified)
                                            <p class="mb-0"><span class="badge bg-success">تم التحقق</span></p>
                                        @else
                                            <p class="mb-0"><span class="badge bg-warning">قيد التحقق</span></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">لا توجد مهارات مسجلة</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


