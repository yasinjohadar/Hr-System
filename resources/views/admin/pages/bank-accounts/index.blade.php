@extends('admin.layouts.master')

@section('page-title')
    الحسابات البنكية
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
                    <h5 class="page-title fs-21 mb-1">الحسابات البنكية</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header align-items-center d-flex gap-3">
                            @can('bank-account-create')
                            <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-primary btn-sm">إضافة حساب بنكي جديد</a>
                            @endcan

                            <div class="flex-shrink-0">
                                <form action="{{ route('admin.bank-accounts.index') }}" method="GET" class="d-flex align-items-center gap-2">
                                    <input type="text" name="search" class="form-control" placeholder="بحث..." value="{{ request('search') }}" style="width: 200px;">
                                    
                                    <select name="employee_id" class="form-select" style="width: 200px;">
                                        <option value="">كل الموظفين</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->full_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <select name="is_primary" class="form-select" style="width: 150px;">
                                        <option value="">كل الحسابات</option>
                                        <option value="1" {{ request('is_primary') == '1' ? 'selected' : '' }}>أساسي</option>
                                        <option value="0" {{ request('is_primary') == '0' ? 'selected' : '' }}>غير أساسي</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">بحث</button>
                                    <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary btn-sm">إعادة تعيين</a>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom">
                                    <thead>
                                        <tr>
                                            <th>الموظف</th>
                                            <th>اسم البنك</th>
                                            <th>رقم الحساب</th>
                                            <th>IBAN</th>
                                            <th>نوع الحساب</th>
                                            <th>حساب أساسي</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($bankAccounts as $account)
                                            <tr>
                                                <td>{{ $account->employee->full_name }}</td>
                                                <td>{{ $account->bank_name_ar ?? $account->bank_name }}</td>
                                                <td>{{ $account->account_number }}</td>
                                                <td>{{ $account->iban ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $account->account_type_name_ar }}</span>
                                                </td>
                                                <td>
                                                    @if($account->is_primary)
                                                        <span class="badge bg-success">نعم</span>
                                                    @else
                                                        <span class="badge bg-secondary">لا</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $account->is_active ? 'success' : 'secondary' }}">
                                                        {{ $account->is_active ? 'نشط' : 'غير نشط' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        @can('bank-account-show')
                                                        <a href="{{ route('admin.bank-accounts.show', $account->id) }}" class="btn btn-sm btn-info">عرض</a>
                                                        @endcan
                                                        @can('bank-account-edit')
                                                        <a href="{{ route('admin.bank-accounts.edit', $account->id) }}" class="btn btn-sm btn-primary">تعديل</a>
                                                        @endcan
                                                        @can('bank-account-delete')
                                                        <form action="{{ route('admin.bank-accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                                        </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">لا توجد حسابات بنكية</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $bankAccounts->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

