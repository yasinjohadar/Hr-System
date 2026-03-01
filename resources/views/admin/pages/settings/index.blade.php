@extends('admin.layouts.master')

@section('page-title')
    الإعدادات
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الإعدادات</h5>
                </div>
            </div>

            <div class="row">
                @foreach ($groups as $group)
                    @php
                        $groupName = match($group) {
                            'general' => 'الإعدادات العامة',
                            'email' => 'إعدادات البريد الإلكتروني',
                            'sms' => 'إعدادات الرسائل النصية',
                            'attendance' => 'إعدادات الحضور',
                            'salary' => 'إعدادات الرواتب',
                            'leave' => 'إعدادات الإجازات',
                            'performance' => 'إعدادات التقييمات',
                            'training' => 'إعدادات التدريب',
                            'recruitment' => 'إعدادات التوظيف',
                            'notification' => 'إعدادات الإشعارات',
                            'system' => 'إعدادات النظام',
                            default => $group,
                        };
                        $groupIcon = match($group) {
                            'general' => 'fas fa-cog',
                            'email' => 'fas fa-envelope',
                            'sms' => 'fas fa-sms',
                            'attendance' => 'fas fa-calendar-check',
                            'salary' => 'fas fa-money-bill-wave',
                            'leave' => 'fas fa-calendar-times',
                            'performance' => 'fas fa-star',
                            'training' => 'fas fa-graduation-cap',
                            'recruitment' => 'fas fa-briefcase',
                            'notification' => 'fas fa-bell',
                            'system' => 'fas fa-server',
                            default => 'fas fa-cog',
                        };
                        $groupColor = match($group) {
                            'general' => 'primary',
                            'email' => 'info',
                            'sms' => 'success',
                            'attendance' => 'warning',
                            'salary' => 'danger',
                            'leave' => 'secondary',
                            'performance' => 'purple',
                            'training' => 'teal',
                            'recruitment' => 'orange',
                            'notification' => 'pink',
                            'system' => 'dark',
                            default => 'primary',
                        };
                    @endphp
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-md bg-{{ $groupColor }} text-white rounded">
                                            <i class="{{ $groupIcon }} fa-lg"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $groupName }}</h6>
                                        <p class="mb-0 text-muted small">
                                            {{ $settings[$group]->count() ?? 0 }} إعداد
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('admin.settings.group', $group) }}" class="btn btn-{{ $groupColor }} btn-sm w-100">
                                        <i class="fas fa-edit me-2"></i>تعديل الإعدادات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .avatar {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-purple {
            background-color: #6f42c1;
            border-color: #6f42c1;
            color: white;
        }
        .btn-teal {
            background-color: #20c997;
            border-color: #20c997;
            color: white;
        }
        .btn-orange {
            background-color: #fd7e14;
            border-color: #fd7e14;
            color: white;
        }
        .btn-pink {
            background-color: #e91e63;
            border-color: #e91e63;
            color: white;
        }
    </style>
@stop


