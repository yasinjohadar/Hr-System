@extends('admin.layouts.master')

@section('page-title')
    إضافة طلب تعيين
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إضافة طلب تعيين</h5>
                </div>
                <a href="{{ route('admin.requisitions.index') }}" class="btn btn-secondary btn-sm">عودة</a>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">بيانات الطلب</h5>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.requisitions.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">القسم <span class="text-danger">*</span></label>
                                            <select name="department_id" class="form-select" required>
                                                <option value="">اختر القسم</option>
                                                @foreach ($departments as $d)
                                                    <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">المنصب <span class="text-danger">*</span></label>
                                            <select name="position_id" class="form-select" required>
                                                <option value="">اختر المنصب</option>
                                                @foreach ($positions as $p)
                                                    <option value="{{ $p->id }}" {{ old('position_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">الفرع</label>
                                    <select name="branch_id" class="form-select">
                                        <option value="">بدون فرع محدد</option>
                                        @foreach ($branches as $b)
                                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">عدد المناصب المطلوبة <span class="text-danger">*</span></label>
                                    <input type="number" name="number_of_positions" class="form-control" min="1" value="{{ old('number_of_positions', 1) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">المبرر <span class="text-danger">*</span></label>
                                    <textarea name="justification" class="form-control" rows="4" required>{{ old('justification') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">حفظ الطلب</button>
                                    <a href="{{ route('admin.requisitions.index') }}" class="btn btn-secondary">إلغاء</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
