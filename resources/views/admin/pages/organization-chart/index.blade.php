@extends('admin.layouts.master')

@section('page-title')
    الهيكل التنظيمي
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">الهيكل التنظيمي</h5>
                </div>
                <div>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary view-type-btn" data-view="department">
                            <i class="fas fa-building me-2"></i>حسب الأقسام
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary view-type-btn" data-view="employee">
                            <i class="fas fa-users me-2"></i>حسب الموظفين
                        </button>
                    </div>
                </div>
            </div>

            <!-- فلترة -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.organization-chart.index') }}" class="row g-3" id="filterForm">
                        <input type="hidden" name="view" id="viewType" value="{{ $viewType }}">
                        <div class="col-md-4">
                            <select name="department_id" class="form-select" id="departmentFilter">
                                <option value="">كل الأقسام</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-secondary w-100" onclick="exportChart()">
                                <i class="fas fa-download me-2"></i>تصدير
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- الهيكل التنظيمي -->
            <div class="card">
                <div class="card-body">
                    <div id="orgChart" style="min-height: 600px; overflow: auto;"></div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/orgchart@3.8.0/dist/css/jquery.orgchart.min.css">
<style>
    .orgchart {
        background: #fff;
    }
    .orgchart .node {
        background: #fff;
        border: 2px solid #007bff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .orgchart .node .title {
        background: #007bff;
        color: #fff;
        padding: 8px;
        border-radius: 4px;
        font-weight: bold;
    }
    .orgchart .node .content {
        padding: 5px;
        color: #333;
    }
    .orgchart .node img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-bottom: 5px;
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/orgchart@3.8.0/dist/js/jquery.orgchart.min.js"></script>
<script>
    let orgChart;
    const viewType = '{{ $viewType }}';

    // تحميل البيانات عند تحميل الصفحة
    $(document).ready(function() {
        loadChart();
        
        // تغيير نوع العرض
        $('.view-type-btn').on('click', function() {
            const newView = $(this).data('view');
            $('#viewType').val(newView);
            $('.view-type-btn').removeClass('active');
            $(this).addClass('active');
            loadChart();
        });
        
        // تفعيل الزر المحدد
        $(`.view-type-btn[data-view="${viewType}"]`).addClass('active');
    });

    // تحميل الهيكل التنظيمي
    function loadChart() {
        const viewType = $('#viewType').val();
        const departmentId = $('#departmentFilter').val();
        
        $.ajax({
            url: '{{ route("admin.organization-chart.get-data") }}',
            method: 'GET',
            data: {
                view: viewType,
                department_id: departmentId
            },
            success: function(data) {
                renderChart(data, viewType);
            },
            error: function(xhr) {
                console.error('Error loading chart:', xhr);
                $('#orgChart').html('<div class="alert alert-danger">حدث خطأ أثناء تحميل البيانات</div>');
            }
        });
    }

    // رسم الهيكل التنظيمي
    function renderChart(data, viewType) {
        $('#orgChart').empty();
        
        if (!data || data.length === 0) {
            $('#orgChart').html('<div class="alert alert-info text-center">لا توجد بيانات للعرض</div>');
            return;
        }

        // تحويل البيانات إلى تنسيق OrgChart
        const chartData = convertToOrgChartFormat(data, viewType);
        
        orgChart = $('#orgChart').orgchart({
            'data': chartData,
            'nodeContent': 'title',
            'pan': true,
            'zoom': true,
            'direction': 't2b',
            'createNode': function($node, data) {
                let content = '';
                if (viewType === 'employee' && data.photo) {
                    content += `<img src="${data.photo}" alt="${data.name}" style="width: 50px; height: 50px; border-radius: 50%; margin-bottom: 5px;"><br>`;
                }
                content += `<div class="title">${data.name}</div>`;
                if (data.title) {
                    content += `<div class="content">${data.title}</div>`;
                }
                if (data.department) {
                    content += `<div class="content"><small>${data.department}</small></div>`;
                }
                $node.html(content);
            }
        });
    }

    // تحويل البيانات إلى تنسيق OrgChart
    function convertToOrgChartFormat(data, viewType) {
        if (viewType === 'employee') {
            return convertEmployeeData(data);
        } else {
            return convertDepartmentData(data);
        }
    }

    // تحويل بيانات الموظفين
    function convertEmployeeData(data) {
        if (data.length === 0) return null;
        
        function buildNode(item) {
            const node = {
                'name': item.name,
                'title': item.title,
                'department': item.department,
                'photo': item.photo
            };
            
            if (item.children && item.children.length > 0) {
                node.children = item.children.map(child => buildNode(child));
            }
            
            return node;
        }
        
        // إذا كان هناك أكثر من جذر، نضيف جذر وهمي
        if (data.length > 1) {
            return {
                'name': 'المنظمة',
                'title': '',
                'children': data.map(item => buildNode(item))
            };
        }
        
        return buildNode(data[0]);
    }

    // تحويل بيانات الأقسام
    function convertDepartmentData(data) {
        if (data.length === 0) return null;
        
        function buildNode(item) {
            const node = {
                'name': item.name,
                'title': item.manager ? `مدير: ${item.manager}` : 'لا يوجد مدير',
                'department': item.name
            };
            
            if (item.children && item.children.length > 0) {
                node.children = item.children.map(child => buildNode(child));
            }
            
            return node;
        }
        
        if (data.length > 1) {
            return {
                'name': 'المنظمة',
                'title': '',
                'children': data.map(item => buildNode(item))
            };
        }
        
        return buildNode(data[0]);
    }

    // تصدير الهيكل
    function exportChart() {
        if (orgChart) {
            // يمكن استخدام html2canvas لتصدير الصورة
            alert('ميزة التصدير قيد التطوير');
        }
    }
</script>
@stop

