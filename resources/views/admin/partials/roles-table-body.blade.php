@forelse ($roles as $role)
    <tr>
        <th scope="row" class="row-number">{{ $roles->firstItem() + $loop->index }}</th>
        <td>
            <span class="admin-badge admin-badge-role" style="font-size: 0.85rem; padding: 0.35rem 0.75rem;">
                <i class="ri-shield-user-line me-1"></i>
                {{ $role->name }}
            </span>
        </td>
        <td>
            <span class="admin-badge admin-badge-muted">{{ $role->permissions_count }} صلاحية</span>
        </td>
        <td>
            <div class="admin-action-group">
                @can('role-edit')
                    <a href="{{ route('roles.edit', $role->id) }}" class="admin-action-btn admin-action-edit" title="تعديل">
                        <i class="ri-edit-line"></i>
                    </a>
                @endcan
                @can('role-delete')
                    <button type="button" class="admin-action-btn admin-action-delete" title="حذف"
                            data-delete-url="{{ route('roles.destroy', $role->id) }}"
                            data-delete-message="هل أنت متأكد من رغبتك في حذف الدور <strong>{{ $role->name }}</strong>؟">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4">
            <div class="admin-empty-state">
                <i class="ri-shield-keyhole-line"></i>
                لا توجد أدوار
            </div>
        </td>
    </tr>
@endforelse
