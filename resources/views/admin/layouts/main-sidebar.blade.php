        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="index.html" class="header-logo">
                    <img src="../assets/images/brand-logos/desktop-logo.png" alt="logo" class="desktop-logo">
                    <img src="../assets/images/brand-logos/toggle-logo.png" alt="logo" class="toggle-logo">
                    <img src="../assets/images/brand-logos/desktop-white.png" alt="logo" class="desktop-white">
                    <img src="../assets/images/brand-logos/toggle-white.png" alt="logo" class="toggle-white">
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>
                    <ul class="main-menu">
                        <!-- Start::slide has-sub - مركز الإدارة -->
                        @canAny(['dashboard-view', 'role-list', 'user-list', 'announcement-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                                <span class="side-menu__label">مركز الإدارة</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('dashboard-view')
                                <li class="slide">
                                    <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                                        <span class="side-menu__label">الصفحة الرئيسية</span>
                                        <span class="badge bg-success ms-auto menu-badge">1</span>
                                    </a>
                                </li>
                                @endcan
                                @can('role-list')
                                <li class="slide">
                                    <a href="{{route("roles.index")}}" class="side-menu__item">الصلاحيات</a>
                                </li>
                                @endcan
                                @can('user-list')
                                <li class="slide">
                                    <a href="{{route("users.index")}}" class="side-menu__item">المستخدمون</a>
                                </li>
                                @endcan
                                @can('announcement-list')
                                <li class="slide">
                                    <a href="{{route("admin.announcements.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
                                        <span class="side-menu__label">إعلانات الشركة</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - إدارة الموارد البشرية -->
                        @canAny(['employee-list', 'dashboard-view', 'salary-list', 'payroll-list', 'salary-component-list', 'tax-setting-list', 'bank-account-list', 'payroll-payment-list', 'payroll-approval-list', 'announcement-list', 'contract-list', 'employee-job-change-list', 'policy-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                                <span class="side-menu__label">إدارة الموارد البشرية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('employee-list')
                                <li class="slide">
                                    <a href="{{route("admin.employees.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                                        <span class="side-menu__label">الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                                @can('contract-list')
                                <li class="slide">
                                    <a href="{{route("admin.contracts.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">العقود</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-job-change-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-job-changes.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        <span class="side-menu__label">النقل والترقية</span>
                                    </a>
                                </li>
                                @endcan
                                @can('policy-list')
                                <li class="slide">
                                    <a href="{{route("admin.policies.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">السياسات واللوائح</span>
                                    </a>
                                </li>
                                @endcan
                                @can('salary-list')
                                <li class="slide">
                                    <a href="{{route("admin.salaries.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">الرواتب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-advance-list')
                                <li class="slide">
                                    <a href="{{ route('admin.employee-advances.index') }}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">سلف الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                                @can('payroll-list')
                                <li class="slide">
                                    <a href="{{route("admin.payrolls.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">كشوف الرواتب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('salary-component-list')
                                <li class="slide">
                                    <a href="{{route("admin.salary-components.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">مكونات الراتب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('tax-setting-list')
                                <li class="slide">
                                    <a href="{{route("admin.tax-settings.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">إعدادات الضرائب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('bank-account-list')
                                <li class="slide">
                                    <a href="{{route("admin.bank-accounts.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">الحسابات البنكية</span>
                                    </a>
                                </li>
                                @endcan
                                @can('payroll-payment-list')
                                <li class="slide">
                                    <a href="{{route("admin.payroll-payments.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">سجلات الدفع</span>
                                    </a>
                                </li>
                                @endcan
                                @can('payroll-approval-list')
                                <li class="slide">
                                    <a href="{{route("admin.payroll-approvals.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg>
                                        <span class="side-menu__label">الموافقات</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - إدارة الإجازات -->
                        @canAny(['leave-type-list', 'leave-request-list', 'leave-balance-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                <span class="side-menu__label">إدارة الإجازات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('leave-type-list')
                                <li class="slide">
                                    <a href="{{route("admin.leave-types.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                        <span class="side-menu__label">أنواع الإجازات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('leave-request-list')
                                <li class="slide">
                                    <a href="{{route("admin.leave-requests.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                        <span class="side-menu__label">طلبات الإجازات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('leave-balance-list')
                                <li class="slide">
                                    <a href="{{route("admin.leave-balances.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                        <span class="side-menu__label">أرصدة الإجازات</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - الحضور والتقييمات والتدريب -->
                        @canAny(['attendance-list', 'shift-list', 'shift-assignment-list', 'attendance-rule-list', 'overtime-list', 'attendance-location-list', 'attendance-break-list', 'performance-review-list', 'training-list', 'training-record-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                <span class="side-menu__label">الحضور والتقييمات والتدريب</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('attendance-list')
                                <li class="slide">
                                    <a href="{{route("admin.attendances.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">الحضور والانصراف</span>
                                    </a>
                                </li>
                                @endcan
                                @can('shift-list')
                                <li class="slide">
                                    <a href="{{route("admin.shifts.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">المناوبات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('shift-assignment-list')
                                <li class="slide">
                                    <a href="{{route("admin.shift-assignments.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">تعيينات المناوبات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('attendance-rule-list')
                                <li class="slide">
                                    <a href="{{route("admin.attendance-rules.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">قواعد الحضور</span>
                                    </a>
                                </li>
                                @endcan
                                @can('overtime-list')
                                <li class="slide">
                                    <a href="{{route("admin.overtimes.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">الساعات الإضافية</span>
                                    </a>
                                </li>
                                @endcan
                                @can('attendance-location-list')
                                <li class="slide">
                                    <a href="{{route("admin.attendance-locations.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        <span class="side-menu__label">مواقع الحضور (GPS)</span>
                                    </a>
                                </li>
                                @endcan
                                @can('attendance-break-list')
                                <li class="slide">
                                    <a href="{{route("admin.attendance-breaks.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/></svg>
                                        <span class="side-menu__label">الاستراحات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('performance-review-list')
                                <li class="slide">
                                    <a href="{{route("admin.performance-reviews.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        <span class="side-menu__label">التقييمات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('training-list')
                                <li class="slide">
                                    <a href="{{route("admin.trainings.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9zm6.82 6L12 16.72 5.18 12 12 7.28 18.82 9z"/></svg>
                                        <span class="side-menu__label">التدريب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('training-record-list')
                                <li class="slide">
                                    <a href="{{route("admin.training-records.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                                        <span class="side-menu__label">سجلات التدريب</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - التوظيف -->
                        @canAny(['requisition-list', 'job-vacancy-list', 'candidate-list', 'job-application-list', 'interview-list', 'offer-letter-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15 10.83 16.62 12 20 8.76V14z"/></svg>
                                <span class="side-menu__label">التوظيف</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('requisition-list')
                                <li class="slide">
                                    <a href="{{ route('admin.requisitions.index') }}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">طلب التعيين</span>
                                    </a>
                                </li>
                                @endcan
                                @can('job-vacancy-list')
                                <li class="slide">
                                    <a href="{{route("admin.job-vacancies.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 6h-2.18c.11-.31.18-.65.18-1a2.996 2.996 0 0 0-5.5-1.65l-.5.67-.5-.68C10.96 2.54 10 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15 10.83 16.62 12 20 8.76V14z"/></svg>
                                        <span class="side-menu__label">الوظائف الشاغرة</span>
                                    </a>
                                </li>
                                @endcan
                                @can('candidate-list')
                                <li class="slide">
                                    <a href="{{route("admin.candidates.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                                        <span class="side-menu__label">المرشحون</span>
                                    </a>
                                </li>
                                @endcan
                                @can('job-application-list')
                                <li class="slide">
                                    <a href="{{route("admin.job-applications.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
                                        <span class="side-menu__label">طلبات التوظيف</span>
                                    </a>
                                </li>
                                @endcan
                                @can('interview-list')
                                <li class="slide">
                                    <a href="{{route("admin.interviews.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">المقابلات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('offer-letter-list')
                                <li class="slide">
                                    <a href="{{route("admin.offer-letters.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">عروض التعيين</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - المزايا والتعويضات -->
                        @canAny(['benefit-type-list', 'employee-benefit-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                <span class="side-menu__label">المزايا والتعويضات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('benefit-type-list')
                                <li class="slide">
                                    <a href="{{route("admin.benefit-types.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">أنواع المزايا</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-benefit-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-benefits.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.72-2.92 0-1.21-.49-2.19-3.55-2.68z"/></svg>
                                        <span class="side-menu__label">مزايا الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - التقارير -->
                        @canAny(['report-view', 'report-dashboard', 'report-employees', 'report-attendance', 'report-salaries', 'report-leaves', 'report-performance', 'report-training', 'report-recruitment', 'report-benefits', 'report-turnover', 'report-training-effectiveness', 'report-kpis'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                                <span class="side-menu__label">التقارير</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('report-view')
                                <li class="slide">
                                    <a href="{{route("admin.reports.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                                        <span class="side-menu__label">التقارير الشاملة</span>
                                    </a>
                                </li>
                                @endcan
                                @can('report-dashboard')
                                <li class="slide">
                                    <a href="{{route("admin.reports.dashboard")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                                        <span class="side-menu__label">لوحة المعلومات</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - الإعدادات -->
                        @can('setting-view')
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                                <span class="side-menu__label">الإعدادات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide">
                                    <a href="{{route("admin.settings.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.07.62-.07.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                                        <span class="side-menu__label">الإعدادات</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - إدارة الموظفين المتقدمة -->
                        @canAny(['employee-document-list', 'employee-skill-list', 'employee-certificate-list', 'employee-goal-list', 'employee-exit-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                                <span class="side-menu__label">إدارة الموظفين المتقدمة</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('employee-document-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-documents.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">المستندات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-skill-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-skills.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        <span class="side-menu__label">المهارات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-certificate-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-certificates.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                                        <span class="side-menu__label">الشهادات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-goal-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-goals.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <span class="side-menu__label">الأهداف</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-exit-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-exits.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
                                        <span class="side-menu__label">إنهاء الخدمة</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - إدارة الأصول -->
                        @canAny(['asset-list', 'asset-assignment-list', 'asset-maintenance-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                                <span class="side-menu__label">إدارة الأصول</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('asset-list')
                                <li class="slide">
                                    <a href="{{route("admin.assets.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-5-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zM9 4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm11 15H4v-2h16v2zm0-5H4V8h5.08L7 10.83 8.62 12 11 8.76l1-1.36 1 1.36L15.38 12 17 10.83 14.92 8H20v6z"/></svg>
                                        <span class="side-menu__label">الأصول</span>
                                    </a>
                                </li>
                                @endcan
                                @can('asset-assignment-list')
                                <li class="slide">
                                    <a href="{{route("admin.asset-assignments.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                        <span class="side-menu__label">توزيع الأصول</span>
                                    </a>
                                </li>
                                @endcan
                                @can('asset-maintenance-list')
                                <li class="slide">
                                    <a href="{{route("admin.asset-maintenances.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.4 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/></svg>
                                        <span class="side-menu__label">صيانة الأصول</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - إدارة المصروفات -->
                        @canAny(['expense-category-list', 'expense-request-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                <span class="side-menu__label">إدارة المصروفات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('expense-category-list')
                                <li class="slide">
                                    <a href="{{route("admin.expense-categories.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">تصنيفات المصروفات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('expense-request-list')
                                <li class="slide">
                                    <a href="{{route("admin.expense-requests.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                                        <span class="side-menu__label">طلبات المصروفات</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->

                        <!-- Start::slide has-sub - الإجراءات التأديبية -->
                        @canAny(['violation-type-list', 'disciplinary-action-list', 'employee-violation-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                <span class="side-menu__label">الإجراءات التأديبية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('violation-type-list')
                                <li class="slide">
                                    <a href="{{route("admin.violation-types.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                        <span class="side-menu__label">أنواع المخالفات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('disciplinary-action-list')
                                <li class="slide">
                                    <a href="{{route("admin.disciplinary-actions.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <span class="side-menu__label">الإجراءات التأديبية</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-violation-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-violations.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <span class="side-menu__label">مخالفات الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->

                        <!-- Start::slide has-sub - إدارة المهام والمشاريع -->
                        @canAny(['project-list', 'task-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                                <span class="side-menu__label">إدارة المهام والمشاريع</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('project-list')
                                <li class="slide">
                                    <a href="{{route("admin.projects.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                                        <span class="side-menu__label">المشاريع</span>
                                    </a>
                                </li>
                                @endcan
                                @can('task-list')
                                <li class="slide">
                                    <a href="{{route("admin.tasks.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                        <span class="side-menu__label">المهام</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->

                        <!-- Start::slide has-sub - عرض البيانات -->
                        @canAny(['organization-chart-view', 'employee-directory-view'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                                <span class="side-menu__label">عرض البيانات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('organization-chart-view')
                                <li class="slide">
                                    <a href="{{route("admin.organization-chart.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>
                                        <span class="side-menu__label">الهيكل التنظيمي</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-directory-view')
                                <li class="slide">
                                    <a href="{{route("admin.employee-directory.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.9c1.16 0 2.1.94 2.1 2.1s-.94 2.1-2.1 2.1S9.9 9.16 9.9 8s.94-2.1 2.1-2.1m0 9c2.97 0 6.1 1.46 6.1 2.1v1.1H5.9V17c0-.64 3.13-2.1 6.1-2.1M12 4C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z"/></svg>
                                        <span class="side-menu__label">دليل الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->

                        <!-- Start::slide has-sub - الميزات المتقدمة -->
                        @canAny(['workflow-list', 'succession-plan-list', 'ticket-list', 'meeting-list', 'feedback-request-list', 'reward-type-list', 'employee-reward-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                <span class="side-menu__label">الميزات المتقدمة</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('workflow-list')
                                <li class="slide">
                                    <a href="{{route("admin.workflows.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                        <span class="side-menu__label">سير العمل</span>
                                    </a>
                                </li>
                                @endcan
                                @can('succession-plan-list')
                                <li class="slide">
                                    <a href="{{route("admin.succession-plans.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        <span class="side-menu__label">تخطيط التعاقب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('ticket-list')
                                <li class="slide">
                                    <a href="{{route("admin.tickets.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                        <span class="side-menu__label">التذاكر</span>
                                    </a>
                                </li>
                                @endcan
                                @can('meeting-list')
                                <li class="slide">
                                    <a href="{{route("admin.meetings.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"/></svg>
                                        <span class="side-menu__label">الاجتماعات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('feedback-request-list')
                                <li class="slide">
                                    <a href="{{route("admin.feedback-requests.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        <span class="side-menu__label">التقييم 360 درجة</span>
                                    </a>
                                </li>
                                @endcan
                                @can('reward-type-list')
                                <li class="slide">
                                    <a href="{{route("admin.reward-types.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        <span class="side-menu__label">أنواع المكافآت</span>
                                    </a>
                                </li>
                                @endcan
                                @can('employee-reward-list')
                                <li class="slide">
                                    <a href="{{route("admin.employee-rewards.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        <span class="side-menu__label">مكافآت الموظفين</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->

                        <!-- Start::slide has-sub - الميزات الإضافية -->
                        @canAny(['onboarding-template-list', 'onboarding-process-list', 'audit-log-list', 'survey-list', 'email-template-list', 'document-template-list', 'department-list', 'position-list', 'branch-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                <span class="side-menu__label">الميزات الإضافية</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('onboarding-template-list')
                                <li class="slide">
                                    <a href="{{route("admin.onboarding-templates.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                        <span class="side-menu__label">قوالب الاستقبال</span>
                                    </a>
                                </li>
                                @endcan
                                @can('onboarding-process-list')
                                <li class="slide">
                                    <a href="{{route("admin.onboarding-processes.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        <span class="side-menu__label">عمليات الاستقبال</span>
                                    </a>
                                </li>
                                @endcan
                                @can('audit-log-list')
                                <li class="slide">
                                    <a href="{{route("admin.audit-logs.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
                                        <span class="side-menu__label">سجلات التدقيق</span>
                                    </a>
                                </li>
                                @endcan
                                @can('survey-list')
                                <li class="slide">
                                    <a href="{{route("admin.surveys.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        <span class="side-menu__label">الاستبيانات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('email-template-list')
                                <li class="slide">
                                    <a href="{{route("admin.email-templates.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                        <span class="side-menu__label">قوالب البريد</span>
                                    </a>
                                </li>
                                @endcan
                                @can('document-template-list')
                                <li class="slide">
                                    <a href="{{route("admin.document-templates.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                        <span class="side-menu__label">قوالب المستندات</span>
                                    </a>
                                </li>
                                @endcan
                                @can('department-list')
                                <li class="slide">
                                    <a href="{{route("admin.departments.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                                        <span class="side-menu__label">الأقسام</span>
                                    </a>
                                </li>
                                @endcan
                                @can('position-list')
                                <li class="slide">
                                    <a href="{{route("admin.positions.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <span class="side-menu__label">المناصب</span>
                                    </a>
                                </li>
                                @endcan
                                @can('branch-list')
                                <li class="slide">
                                    <a href="{{route("admin.branches.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                        <span class="side-menu__label">الفروع</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->
                        
                        <!-- Start::slide has-sub - التقويم -->
                        @can('calendar-list')
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                                <span class="side-menu__label">التقويم</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide">
                                    <a href="{{route("admin.calendar-events.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
                                        <span class="side-menu__label">الأحداث والتذكيرات</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        <!-- End::slide has-sub -->

                        <!-- Start::slide - التصدير -->
                        @can('export-data')
                        <li class="slide">
                            <a href="{{route("admin.export.index")}}" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                <span class="side-menu__label">تصدير البيانات</span>
                            </a>
                        </li>
                        @endcan
                        <!-- End::slide -->

                        <!-- Start::slide has-sub - الإعدادات العامة -->
                        @canAny(['country-list', 'currency-list'])
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                <span class="side-menu__label">الإعدادات العامة</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                @can('country-list')
                                <li class="slide">
                                    <a href="{{route("admin.countries.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                        <span class="side-menu__label">الدول</span>
                                    </a>
                                </li>
                                @endcan
                                @can('currency-list')
                                <li class="slide">
                                    <a href="{{route("admin.currencies.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                                        <span class="side-menu__label">العملات</span>
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endcanAny
                        <!-- End::slide has-sub -->









                        {{-- <!-- Start::slide -->
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 12c0 4.08 3.06 7.44 7 7.93V4.07C7.05 4.56 4 7.92 4 12z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.74c.08.33.15.66.19 1H13v-1zm0 9.93V19h2.87c-.87.48-1.84.8-2.87.93zM18.24 17H13v-1h5.92c-.2.35-.43.69-.68 1zm1.5-3H13v-1h6.93c-.04.34-.11.67-.19 1z"/></svg>
                                <span class="side-menu__label">الاعدادات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide side-menu__label1">
                                    <a href="javascript:void(0);">Apps</a>
                                </li>
                                <li class="slide">
                                    <a href="cards.html" class="side-menu__item">الاعدادات العامة</a>
                                </li>
                                <li class="slide">
                                    <a href="{{route("roles.index")}}" class="side-menu__item">الصلاحيات</a>
                                </li>
                                <li class="slide">
                                    <a href="{{route("users.index")}}" class="side-menu__item">المستخدمون</a>
                                </li>

                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- End::slide --> --}}


                    </ul>
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->

<style>
/* تمييز الرابط النشط */
.side-menu__item.active {
    background-color: rgba(59, 130, 246, 0.15) !important;
    color: #3b82f6 !important;
    font-weight: 600;
}

.side-menu__item.active .side-menu__icon {
    fill: #3b82f6 !important;
    color: #3b82f6 !important;
}

.side-menu__item.active .side-menu__label {
    color: #3b82f6 !important;
}

.side-menu__item.active::before {
    content: "";
    width: 3px;
    height: 100%;
    background: #3b82f6;
    position: absolute;
    inset-inline-start: 0;
    top: 0;
}

/* إظهار القوائم المنسدلة عند الفتح */
.slide-menu.child1 {
    display: none !important;
}

.slide.has-sub.open .slide-menu.child1 {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    transform: translate(0, 0) !important;
    height: auto !important;
    overflow: visible !important;
}

.slide.has-sub.has-active .slide-menu.child1 {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    transform: translate(0, 0) !important;
    height: auto !important;
    overflow: visible !important;
}

.slide.has-sub.open > .side-menu__item .side-menu__angle {
    transform: rotate(90deg) !important;
    color: var(--primary-color);
}

.slide.has-sub.has-active > .side-menu__item {
    color: var(--primary-color);
}

.slide.has-sub.has-active.open > .side-menu__item .side-menu__angle {
    transform: rotate(90deg) !important;
    color: var(--primary-color);
}

/* إصلاح أي مشاكل في العرض */
.slide.has-sub .slide-menu {
    max-height: none !important;
}
</style>

<script>
(function() {
    'use strict';
    
    // الانتظار حتى يتم تحميل الصفحة بالكامل
    function initSidebar() {
        // الحصول على URL الحالي
        const currentUrl = window.location.href;
        const currentPath = window.location.pathname;
        
        // إزالة التمييز من جميع الروابط
        document.querySelectorAll('.side-menu__item').forEach(item => {
            item.classList.remove('active');
        });
        
        // البحث عن الرابط المطابق وإضافة class active
        let activeItem = null;
        document.querySelectorAll('.side-menu__item').forEach(item => {
            const href = item.getAttribute('href');
            if (href && href !== 'javascript:void(0);' && href !== '#' && href !== '') {
                try {
                    // مقارنة المسارات
                    let itemPath = href;
                    if (href.startsWith('http')) {
                        itemPath = new URL(href).pathname;
                    } else if (href.startsWith('/')) {
                        itemPath = href;
                    } else {
                        itemPath = '/' + href;
                    }
                    
                    if (currentPath === itemPath || 
                        currentPath.startsWith(itemPath + '/') || 
                        currentUrl.includes(href) ||
                        currentPath.includes(itemPath)) {
                        item.classList.add('active');
                        activeItem = item;
                        
                        // فتح القائمة المنسدلة التي تحتوي على هذا الرابط
                        const parentSlide = item.closest('.slide.has-sub');
                        if (parentSlide) {
                            parentSlide.classList.add('open', 'has-active');
                            const slideMenu = parentSlide.querySelector('.slide-menu');
                            if (slideMenu) {
                                slideMenu.style.display = 'block';
                                slideMenu.style.visibility = 'visible';
                                slideMenu.style.opacity = '1';
                            }
                        }
                    }
                } catch(e) {
                    // في حالة خطأ في URL، استخدام مقارنة بسيطة
                    if (currentPath.includes(href) || currentUrl.includes(href)) {
                        item.classList.add('active');
                        activeItem = item;
                        
                        const parentSlide = item.closest('.slide.has-sub');
                        if (parentSlide) {
                            parentSlide.classList.add('open', 'has-active');
                            const slideMenu = parentSlide.querySelector('.slide-menu');
                            if (slideMenu) {
                                slideMenu.style.display = 'block';
                                slideMenu.style.visibility = 'visible';
                                slideMenu.style.opacity = '1';
                            }
                        }
                    }
                }
            }
        });
        
        // التمرير إلى الرابط النشط
        if (activeItem) {
            setTimeout(() => {
                const sidebarScroll = document.getElementById('sidebar-scroll');
                if (sidebarScroll) {
                    const itemTop = activeItem.offsetTop;
                    const sidebarHeight = sidebarScroll.clientHeight;
                    sidebarScroll.scrollTo({
                        top: itemTop - (sidebarHeight / 2),
                        behavior: 'smooth'
                    });
                }
            }, 500);
        }
    }
    
    // تشغيل الكود بعد تحميل الصفحة
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initSidebar, 800);
        });
    } else {
        setTimeout(initSidebar, 800);
    }
    
    // فتح القوائم المنسدلة عند النقر - استخدام event delegation
    document.addEventListener('click', function(e) {
        const clickedItem = e.target.closest('.slide.has-sub > .side-menu__item');
        if (clickedItem) {
            const href = clickedItem.getAttribute('href');
            if (href === 'javascript:void(0);' || href === '#' || !href) {
                e.preventDefault();
                e.stopPropagation();
                const parentSlide = clickedItem.closest('.slide.has-sub');
                if (parentSlide) {
                    const isOpen = parentSlide.classList.contains('open');
                    
                    // تبديل حالة القائمة المنسدلة
                    if (isOpen) {
                        parentSlide.classList.remove('open');
                        const slideMenu = parentSlide.querySelector('.slide-menu');
                        if (slideMenu) {
                            slideMenu.style.display = 'none';
                            slideMenu.style.visibility = 'hidden';
                            slideMenu.style.opacity = '0';
                        }
                    } else {
                        parentSlide.classList.add('open');
                        const slideMenu = parentSlide.querySelector('.slide-menu');
                        if (slideMenu) {
                            slideMenu.style.display = 'block';
                            slideMenu.style.visibility = 'visible';
                            slideMenu.style.opacity = '1';
                            // إجبار إظهار القائمة
                            slideMenu.removeAttribute('style');
                            slideMenu.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                        }
                    }
                }
            }
        }
    });
    
    // إضافة event listener مباشر على الروابط
    setTimeout(function() {
        document.querySelectorAll('.slide.has-sub > .side-menu__item').forEach(function(item) {
            const href = item.getAttribute('href');
            if (href === 'javascript:void(0);' || href === '#' || !href) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const parentSlide = this.closest('.slide.has-sub');
                    if (parentSlide) {
                        const isOpen = parentSlide.classList.contains('open');
                        const slideMenu = parentSlide.querySelector('.slide-menu');
                        
                        if (isOpen) {
                            parentSlide.classList.remove('open');
                            if (slideMenu) {
                                slideMenu.style.display = 'none';
                            }
                        } else {
                            parentSlide.classList.add('open');
                            if (slideMenu) {
                                slideMenu.style.display = 'block';
                                slideMenu.style.visibility = 'visible';
                                slideMenu.style.opacity = '1';
                            }
                        }
                    }
                }, true);
            }
        });
    }, 1000);
})();
</script>
