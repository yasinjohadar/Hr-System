@extends('employee.layouts.master')

@section('page-title')
    سجلات وقتي على المشاريع
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">سجلات وقتي على المشاريع</h5>
                </div>
                <div>
                    <a href="{{ route('employee.projects') }}" class="btn btn-secondary btn-sm">المشاريع</a>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="get" action="{{ route('employee.project-time.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">المشروع</label>
                            <select name="project_id" class="form-select">
                                <option value="">— الكل —</option>
                                @foreach ($accessibleProjects as $p)
                                    <option value="{{ $p->id }}" @selected(request('project_id') == $p->id)>{{ $p->name_ar ?? $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">من تاريخ</label>
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">إلى تاريخ</label>
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">تصفية</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">النتائج ({{ $entries->total() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>المشروع</th>
                                    <th>الساعات</th>
                                    <th>المهمة</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->worked_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($entry->project)
                                                <a href="{{ route('employee.projects.show', $entry->project) }}">{{ $entry->project->name_ar ?? $entry->project->name }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $entry->hours, 2) }}</td>
                                        <td>{{ $entry->task ? ($entry->task->title_ar ?? $entry->task->title) : '—' }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($entry->description ?? '', 50) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">لا توجد سجلات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>
@stop
