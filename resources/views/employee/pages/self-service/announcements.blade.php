@extends('employee.layouts.master')

@section('page-title')
    الإعلانات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">لوحة الإعلانات</h5>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">الإعلانات ({{ $announcements->total() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($announcements as $announcement)
                        <div class="border rounded p-3 mb-3">
                            <h6 class="mb-2">{{ $announcement->title }}</h6>
                            <p class="text-muted small mb-2">
                                {{ $announcement->publish_date?->format('Y-m-d') }}
                                @if($announcement->expiry_date)
                                    — حتى {{ $announcement->expiry_date->format('Y-m-d') }}
                                @endif
                            </p>
                            <div class="text-muted">
                                {!! nl2br(e($announcement->content)) !!}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">لا توجد إعلانات حالياً.</p>
                    @endforelse
                    @if($announcements->hasPages())
                        <div class="mt-3">{{ $announcements->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
