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


        @yield('content')


        @include('employee.layouts.footer')

    </div>
    @include('employee.layouts.footer-scripts')


</body>

</html>
