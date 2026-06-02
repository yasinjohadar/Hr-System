@extends('admin.layouts.master')
@section('page-title', 'تقارير مجدولة')
@section('content')
<div class="container-fluid">
    <h5 class="my-4">تقارير مجدولة</h5>
    <div class="row">
        <div class="col-lg-5">
            <div class="card p-3 mb-4">
                <form method="POST" action="{{ route('admin.scheduled-reports.store') }}">
                    @csrf
                    <div class="mb-2"><input name="name" class="form-control" placeholder="اسم الجدولة" required></div>
                    <div class="mb-2">
                        <select name="report_type" class="form-select" required>
                            <option value="employees">موظفين</option>
                            <option value="attendance">حضور</option>
                            <option value="leaves">إجازات</option>
                            <option value="payroll">رواتب</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <select name="frequency" class="form-select" required>
                            <option value="daily">يومي</option>
                            <option value="weekly">أسبوعي</option>
                            <option value="monthly">شهري</option>
                        </select>
                    </div>
                    <div class="mb-2"><input name="recipients" class="form-control" placeholder="إيميلات مفصولة بفاصلة" required></div>
                    <button class="btn btn-primary btn-sm">إضافة</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <table class="table mb-0">
                    <thead><tr><th>الاسم</th><th>النوع</th><th>التكرار</th><th></th></tr></thead>
                    <tbody>
                    @foreach($reports as $r)
                        <tr>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->report_type }}</td>
                            <td>{{ $r->frequency }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.scheduled-reports.destroy', $r->id) }}">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
