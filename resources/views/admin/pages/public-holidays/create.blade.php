@extends('admin.layouts.master')
@section('page-title', 'إضافة عطلة')
@section('content')
<div class="container-fluid">
    <div class="card p-4 col-lg-8">
        <form method="POST" action="{{ route('admin.public-holidays.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label">الاسم</label><input name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">الاسم بالعربية</label><input name="name_ar" class="form-control"></div>
            <div class="mb-3"><label class="form-label">التاريخ</label><input type="date" name="holiday_date" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">الدولة</label><select name="country_id" class="form-select"><option value="">—</option>@foreach($countries as $c)<option value="{{ $c->id }}">{{ $c->name_ar ?? $c->name }}</option>@endforeach</select></div>
            <div class="mb-3"><label class="form-label">الفرع</label><select name="branch_id" class="form-select"><option value="">—</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
            <div class="form-check mb-3"><input type="checkbox" name="is_recurring" value="1" class="form-check-input" id="rec"><label for="rec" class="form-check-label">تتكرر سنوياً</label></div>
            <button class="btn btn-primary">حفظ</button>
        </form>
    </div>
</div>
@endsection
