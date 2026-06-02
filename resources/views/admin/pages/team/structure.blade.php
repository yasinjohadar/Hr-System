@extends('admin.layouts.master')

@section('page-title')
    هيكل القسم
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div>
                    <h5 class="page-title fs-21 mb-1">هيكل القسم</h5>
                    <p class="text-muted fs-13 mb-0">الهيكل التنظيمي للأقسام المُدارة</p>
                </div>
                <a href="{{ route('admin.team.dashboard') }}" class="btn btn-outline-primary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>العودة للوحة التحكم
                </a>
            </div>

            <div class="row">
                @forelse($tree as $node)
                    <div class="col-xl-4 col-lg-6 mb-4">
                        @include('admin.pages.team.partials.department-node', ['node' => $node, 'level' => 0])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card custom-card text-center py-5">
                            <i class="ri-building-line fs-40 d-block mb-3 text-muted"></i>
                            <h5>لا توجد أقسام مُدارة</h5>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@stop
