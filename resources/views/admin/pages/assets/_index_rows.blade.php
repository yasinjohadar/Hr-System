@forelse ($assets as $asset)
    <tr>
        <td>{{ ($assets->firstItem() ?? 0) + $loop->index }}</td>
        <td><strong>{{ $asset->asset_code }}</strong></td>
        <td>{{ $asset->name_ar ?? $asset->name }}</td>
        <td><span class="badge bg-info">{{ $asset->category_name_ar }}</span></td>
        <td>
            @if ($asset->manufacturer || $asset->model)
                {{ $asset->manufacturer }} {{ $asset->model }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ $asset->serial_number ?? '-' }}</td>
        <td>
            @if ($asset->branch)
                {{ $asset->branch->name }}
            @elseif ($asset->department)
                {{ $asset->department->name }}
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @php
                $statusClass = match ($asset->status) {
                    'available' => 'success',
                    'assigned' => 'primary',
                    'maintenance' => 'warning',
                    'disposed' => 'secondary',
                    default => 'danger',
                };
            @endphp
            <span class="badge bg-{{ $statusClass }}">
                {{ $asset->status_name_ar }}
            </span>
        </td>
        <td>
            @if ($asset->currentEmployee())
                <a href="{{ route('admin.employees.show', $asset->currentEmployee()->id) }}">
                    {{ $asset->currentEmployee()->full_name }}
                </a>
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>
            @can('asset-show')
                <a href="{{ route('admin.assets.show', $asset->id) }}" class="btn btn-sm btn-primary" title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
            @endcan
            @can('asset-edit')
                <a href="{{ route('admin.assets.edit', $asset->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
            @endcan
            @can('asset-delete')
                <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $asset->id }}" title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center">لا توجد أصول</td>
    </tr>
@endforelse
