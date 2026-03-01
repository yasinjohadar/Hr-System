@extends('admin.layouts.master')

@section('page-title')
    التقويم
@stop

@section('css')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />
<style>
    /* إعادة تعيين كامل للصفحة */
    body .page .main-content.app-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    body .page .main-content.app-content .container-fluid {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
    }
    
    /* إزالة padding من page-header */
    body .page .main-content .page-header-breadcrumb {
        padding: 15px 20px !important;
        margin: 0 !important;
        margin-bottom: 0 !important;
        background: white;
        border-bottom: 1px solid #e9ecef;
    }
    
    /* تحسين التقويم الرئيسي - قواعد قوية جداً */
    body .page .main-content #calendar,
    body #calendar,
    #calendar {
        width: 100vw !important;
        max-width: 100vw !important;
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        position: relative !important;
        display: block !important;
    }
    
    /* إزالة padding من card - قواعد قوية */
    body .page .main-content .card,
    body .main-content .card {
        margin: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    body .page .main-content .card-body,
    body .main-content .card-body {
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    
    /* إزالة row و col padding - قواعد قوية */
    body .page .main-content .row,
    body .main-content .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        width: 100% !important;
    }
    
    body .page .main-content .col-12,
    body .page .main-content .col-xl-12,
    body .main-content .col-12,
    body .main-content .col-xl-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    
    /* تحسين شريط الأدوات */
    .fc-toolbar-title {
        font-size: 1.75rem !important;
        font-weight: 600 !important;
        color: var(--primary-color, #0162e8);
    }
    
    /* تحسين الأزرار */
    .fc-button {
        padding: 8px 16px !important;
        border-radius: 6px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        border: none !important;
    }
    
    .fc-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
    }
    
    .fc-button-primary {
        background-color: var(--primary-color, #0162e8) !important;
    }
    
    .fc-button-primary:hover {
        background-color: var(--primary-color, #0162e8) !important;
        opacity: 0.9;
    }
    
    .fc-button-active {
        background-color: var(--primary-color, #0162e8) !important;
        opacity: 0.85;
    }
    
    /* تحسين الأحداث */
    .fc-event {
        cursor: pointer;
        border-radius: 4px !important;
        padding: 3px 6px !important;
        border: none !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
    }
    
    .fc-event:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* تحسين أيام التقويم */
    .fc-daygrid-day {
        transition: background-color 0.2s ease;
    }
    
    .fc-daygrid-day:hover {
        background-color: rgba(1, 98, 232, 0.05) !important;
    }
    
    .fc-day-today {
        background-color: rgba(1, 98, 232, 0.1) !important;
    }
    
    /* تحسين رؤوس الأعمدة */
    .fc-col-header-cell {
        padding: 12px 8px !important;
        font-weight: 600 !important;
        background-color: rgba(1, 98, 232, 0.05) !important;
    }
    
    /* تحسين حجم الخلايا */
    .fc-daygrid-day-frame {
        min-height: 120px !important;
    }
    
    /* جعل التقويم يستخدم كامل الارتفاع والعرض - قواعد قوية جداً */
    body .page .main-content #calendar .fc,
    body #calendar .fc,
    #calendar .fc,
    .fc {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
        display: block !important;
    }
    
    body .page .main-content #calendar .fc-view-harness,
    body #calendar .fc-view-harness,
    #calendar .fc-view-harness,
    .fc-view-harness {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
        min-height: calc(100vh - 180px) !important;
        height: calc(100vh - 180px) !important;
        display: block !important;
    }
    
    body .page .main-content #calendar .fc-scroller,
    body #calendar .fc-scroller,
    #calendar .fc-scroller,
    .fc-scroller {
        height: 100% !important;
        width: 100% !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }
    
    body .page .main-content #calendar .fc-scroller-liquid-absolute,
    body #calendar .fc-scroller-liquid-absolute,
    #calendar .fc-scroller-liquid-absolute,
    .fc-scroller-liquid-absolute {
        position: relative !important;
        height: 100% !important;
        width: 100% !important;
    }
    
    /* جعل التقويم يستخدم كامل العرض - قواعد قوية */
    body .page .main-content #calendar .fc-daygrid,
    body #calendar .fc-daygrid,
    #calendar .fc-daygrid,
    .fc-daygrid {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
    }
    
    body .page .main-content #calendar .fc-daygrid-body,
    body #calendar .fc-daygrid-body,
    #calendar .fc-daygrid-body,
    .fc-daygrid-body {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
    }
    
    body .page .main-content #calendar .fc-scrollgrid,
    body #calendar .fc-scrollgrid,
    #calendar .fc-scrollgrid,
    .fc-scrollgrid {
        width: 100vw !important;
        max-width: 100vw !important;
        min-width: 100vw !important;
        table-layout: fixed !important;
    }
    
    body .page .main-content #calendar .fc-scrollgrid-section,
    body #calendar .fc-scrollgrid-section,
    #calendar .fc-scrollgrid-section,
    .fc-scrollgrid-section {
        width: 100vw !important;
        max-width: 100vw !important;
    }
    
    body .page .main-content #calendar .fc-col-header,
    body #calendar .fc-col-header,
    #calendar .fc-col-header,
    .fc-col-header {
        width: 100vw !important;
        max-width: 100vw !important;
        table-layout: fixed !important;
    }
    
    body .page .main-content #calendar .fc-col-header-cell,
    body #calendar .fc-col-header-cell,
    #calendar .fc-col-header-cell,
    .fc-col-header-cell {
        width: calc(100vw / 7) !important;
        min-width: calc(100vw / 7) !important;
        max-width: calc(100vw / 7) !important;
    }
    
    body .page .main-content #calendar .fc-daygrid-day,
    body #calendar .fc-daygrid-day,
    #calendar .fc-daygrid-day,
    .fc-daygrid-day {
        width: calc(100vw / 7) !important;
        min-width: calc(100vw / 7) !important;
        max-width: calc(100vw / 7) !important;
    }
    
    /* تحسين العنوان */
    .fc-daygrid-day-number {
        padding: 8px !important;
        font-weight: 500 !important;
        font-size: 1rem !important;
    }
    
    /* تحسين حجم النصوص */
    .fc-col-header-cell-cushion {
        font-size: 0.95rem !important;
        font-weight: 600 !important;
    }
    
    .fc-event-title {
        font-size: 0.875rem !important;
        padding: 2px 4px !important;
    }
    
    /* تحسين المسافات */
    .fc-daygrid-day-top {
        padding: 4px 8px !important;
    }
    
    /* تحسين الأحداث في اليوم */
    .fc-daygrid-event {
        margin: 2px 0 !important;
    }
    
    /* تحسين responsive للشاشات الصغيرة */
    @media (max-width: 1200px) {
        .fc-view-harness {
            min-height: calc(100vh - 200px) !important;
            height: calc(100vh - 200px) !important;
        }
        
        #calendar {
            min-height: calc(100vh - 120px) !important;
            height: calc(100vh - 120px) !important;
        }
    }
    
    @media (max-width: 992px) {
        .fc-toolbar {
            flex-wrap: wrap !important;
        }
        
        .fc-toolbar-chunk {
            margin-bottom: 10px !important;
        }
        
        .fc-view-harness {
            min-height: calc(100vh - 250px) !important;
            height: calc(100vh - 250px) !important;
        }
    }
    
    @media (max-width: 768px) {
        .page-header-breadcrumb {
            padding: 10px 15px !important;
        }
        
        #calendar {
            min-height: calc(100vh - 100px) !important;
            height: calc(100vh - 100px) !important;
        }
        
        .fc-view-harness {
            min-height: calc(100vh - 200px) !important;
            height: calc(100vh - 200px) !important;
        }
        
        .fc-toolbar-title {
            font-size: 1.25rem !important;
        }
        
        .fc-button {
            padding: 6px 12px !important;
            font-size: 0.875rem !important;
        }
        
        .fc-daygrid-day-frame {
            min-height: 80px !important;
        }
        
        .fc-event-title {
            font-size: 0.75rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .page-header-breadcrumb {
            padding: 8px 10px !important;
        }
        
        .page-title {
            font-size: 1rem !important;
        }
        
        #calendar {
            min-height: calc(100vh - 80px) !important;
            height: calc(100vh - 80px) !important;
        }
        
        .fc-view-harness {
            min-height: calc(100vh - 180px) !important;
            height: calc(100vh - 180px) !important;
        }
        
        .fc-toolbar {
            display: block !important;
        }
        
        .fc-toolbar-chunk {
            width: 100% !important;
            text-align: center !important;
            margin-bottom: 8px !important;
        }
        
        .fc-button {
            padding: 5px 10px !important;
            font-size: 0.8rem !important;
            margin: 2px !important;
        }
        
        .fc-daygrid-day-frame {
            min-height: 60px !important;
        }
        
        .fc-daygrid-day-number {
            font-size: 0.875rem !important;
            padding: 4px !important;
        }
        
        .fc-col-header-cell-cushion {
            font-size: 0.8rem !important;
        }
    }
</style>
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">التقويم</h5>
                </div>
                <div>
                    @can('calendar-create')
                    <a href="{{ route('admin.calendar-events.create') }}" class="btn btn-primary btn-sm">
                        <i class="fe fe-plus"></i> إضافة حدث جديد
                    </a>
                    @endcan
                </div>
            </div>

            <div class="row g-0">
                <div class="col-12">
                    <div class="card border-0" style="margin-bottom: 0;">
                        <div class="card-body p-0">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/ar.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        events: '{{ route("admin.calendar-events.api.events") }}',
        eventClick: function(info) {
            window.location.href = info.event.url;
        },
        eventDisplay: 'block',
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: null,
        handleWindowResize: true,
        navLinks: true,
        dayMaxEvents: true,
        editable: false,
        selectable: false,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        dayHeaderFormat: { weekday: 'long' },
        firstDay: 6, // السبت كأول يوم
        weekNumbers: false,
        weekNumberCalculation: 'ISO'
    });
    calendar.render();
    
    // إعادة حساب الحجم عند تغيير حجم النافذة
    function resizeCalendar() {
        var headerHeight = document.querySelector('.page-header-breadcrumb')?.offsetHeight || 80;
        var viewportHeight = window.innerHeight;
        var calendarHeight = viewportHeight - headerHeight - 20;
        
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            calendarEl.style.height = calendarHeight + 'px';
            calendarEl.style.minHeight = calendarHeight + 'px';
        }
        
        var viewHarness = document.querySelector('.fc-view-harness');
        if (viewHarness) {
            viewHarness.style.height = calendarHeight + 'px';
            viewHarness.style.minHeight = calendarHeight + 'px';
        }
        
        calendar.updateSize();
    }
    
    // استدعاء عند التحميل
    setTimeout(resizeCalendar, 100);
    
    // استدعاء عند تغيير حجم النافذة
    window.addEventListener('resize', function() {
        resizeCalendar();
    });
    
    // إعادة الحساب بعد render
    setTimeout(function() {
        resizeCalendar();
    }, 500);
});
</script>
@stop

