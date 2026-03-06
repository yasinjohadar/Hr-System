<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="light" data-toggled="close">

<head>

    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('page-title')</title>
    <meta name="Description" content="أفضل موقع للاعلانات المبوبة">
    <meta name="Author" content="claudSoft">
    <meta name="keywords" content=" نظام إدارة الموارد البشرية ">

    @include('employee.layouts.head')
</head>

<body>


    @include('employee.layouts.switcher')


    <!-- Loader -->
    <div id="loader">
        <img src="{{asset('assets/images/media/loader.svg')}}" alt="">
    </div>
    <!-- Loader -->

    <div class="page">


        @include('employee.layouts.main-header')



        @include('employee.layouts.offcanvas-sidebar')



        @include('employee.layouts.main-sidebar')


        @if(session('impersonator_id'))
        <div class="alert alert-warning alert-dismissible fade show rounded-0 mb-0 border-0" role="alert">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span><i class="fas fa-user-secret me-2"></i>أنت تدخل بحساب الموظف. للعودة إلى لوحة الإدارة:</span>
                <form action="{{ route('leave-impersonation') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-dark">خروج من حساب الموظف</button>
                </form>
            </div>
        </div>
        @endif

        @yield('content')


        @include('employee.layouts.footer')

    </div>
    @include('employee.layouts.footer-scripts')


</body>

</html>
