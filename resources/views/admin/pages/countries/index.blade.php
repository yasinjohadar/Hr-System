@extends('admin.layouts.master')

@section('page-title')
    قائمة الدول
@stop

@section('css')
@stop

@section('content')
    @if (\Session::has('success'))
        <div class="alert alert-success">
            <ul>
                <li>{!! \Session::get('success') !!}</li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">كافة الدول</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('country-create')
                            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary btn-sm">إضافة دولة جديدة</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.countries.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input style="width: 300px" type="text" name="query" class="form-control"
                                        placeholder="بحث بالاسم أو الكود" value="{{ request('query') }}">
                                    <select name="is_active" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">بحث</button>
                                    <a href="{{ route('admin.countries.index') }}" class="btn btn-danger">مسح</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle table-nowrap mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>العلم</th>
                                            <th>اسم الدولة</th>
                                            <th>الكود</th>
                                            <th>رمز الهاتف</th>
                                            <th>العملة</th>
                                            <th>الحالة</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($countries as $country)
                                            <tr>
                                                <th>{{ $loop->iteration }}</th>
                                                <td>
                                                    @if ($country->flag)
                                                        <span style="font-size: 24px;">{{ $country->flag }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $country->code }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $country->name_ar ?? $country->name }}</strong>
                                                    @if ($country->name_ar && $country->name_ar != $country->name)
                                                        <br><small class="text-muted">({{ $country->name }})</small>
                                                    @endif
                                                </td>
                                                <td><span class="badge bg-info">{{ $country->code }}</span></td>
                                                <td>{{ $country->phone_code ?? '-' }}</td>
                                                <td>{{ $country->currency_code ?? '-' }}</td>
                                                <td>
                                                    @if ($country->is_active)
                                                        <span class="badge bg-success">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger">غير نشط</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('country-edit')
                                                    <a class="btn btn-info btn-sm me-1" href="{{ route('admin.countries.edit', $country->id) }}">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    @endcan
                                                    @can('country-delete')
                                                    <a class="btn btn-danger btn-sm me-1" data-bs-toggle="modal" data-bs-target="#delete{{ $country->id }}">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                            @include('admin.pages.countries.delete')
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-danger fw-bold">لا توجد بيانات متاحة</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="mt-3">
                                    {{ $countries->withQueryString()->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
@stop

