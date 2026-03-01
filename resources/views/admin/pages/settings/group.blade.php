@extends('admin.layouts.master')

@section('page-title')
    إعدادات {{ $group }}
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
                <div class="my-auto">
                    <h5 class="page-title fs-21 mb-1">إعدادات {{ $group }}</h5>
                </div>
                <div>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right me-2"></i>العودة للإعدادات
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update-group', $group) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            @foreach ($settings as $setting)
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            {{ $setting->label_ar ?? $setting->label }}
                                            @if ($setting->is_required)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        
                                        @if ($setting->type == 'textarea')
                                            <textarea 
                                                name="{{ $setting->key }}" 
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                rows="3"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >{{ old($setting->key, $setting->value) }}</textarea>
                                        
                                        @elseif ($setting->type == 'boolean')
                                            <div class="form-check form-switch">
                                                <input 
                                                    type="checkbox" 
                                                    name="{{ $setting->key }}" 
                                                    class="form-check-input"
                                                    id="{{ $setting->key }}"
                                                    value="1"
                                                    {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="{{ $setting->key }}">
                                                    {{ $setting->value == '1' ? 'مفعل' : 'معطل' }}
                                                </label>
                                            </div>
                                        
                                        @elseif ($setting->type == 'select')
                                            <select 
                                                name="{{ $setting->key }}" 
                                                class="form-select @error($setting->key) is-invalid @enderror"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >
                                                <option value="">اختر...</option>
                                                @if ($setting->options)
                                                    @foreach ($setting->options as $option)
                                                        <option value="{{ $option }}" 
                                                            {{ old($setting->key, $setting->value) == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        
                                        @elseif ($setting->type == 'number')
                                            <input 
                                                type="number" 
                                                name="{{ $setting->key }}" 
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                value="{{ old($setting->key, $setting->value) }}"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >
                                        
                                        @elseif ($setting->type == 'email')
                                            <input 
                                                type="email" 
                                                name="{{ $setting->key }}" 
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                value="{{ old($setting->key, $setting->value) }}"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >
                                        
                                        @elseif ($setting->type == 'url')
                                            <input 
                                                type="url" 
                                                name="{{ $setting->key }}" 
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                value="{{ old($setting->key, $setting->value) }}"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >
                                        
                                        @else
                                            <input 
                                                type="text" 
                                                name="{{ $setting->key }}" 
                                                class="form-control @error($setting->key) is-invalid @enderror"
                                                value="{{ old($setting->key, $setting->value) }}"
                                                {{ $setting->is_required ? 'required' : '' }}
                                            >
                                        @endif

                                        @if ($setting->description_ar || $setting->description)
                                            <small class="form-text text-muted">
                                                {{ $setting->description_ar ?? $setting->description }}
                                            </small>
                                        @endif

                                        @error($setting->key)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-4">
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary px-4 me-2">إلغاء</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop


