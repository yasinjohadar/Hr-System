@extends('admin.layouts.master')
@section('page-title', 'العطل الرسمية')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between my-4">
        <h5>العطل الرسمية</h5>
        <a href="{{ route('admin.public-holidays.create') }}" class="btn btn-primary btn-sm">إضافة</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>الاسم</th><th>التاريخ</th><th>الدولة</th><th>الفرع</th><th></th></tr></thead>
                <tbody>
                @foreach($holidays as $h)
                    <tr>
                        <td>{{ $h->name_ar ?? $h->name }}</td>
                        <td>{{ $h->holiday_date->format('Y-m-d') }}</td>
                        <td>{{ $h->country?->name ?? '—' }}</td>
                        <td>{{ $h->branch?->name ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.public-holidays.destroy', $h->id) }}" class="d-inline" onsubmit="return confirm('حذف؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $holidays->links() }}
    </div>
</div>
@endsection
