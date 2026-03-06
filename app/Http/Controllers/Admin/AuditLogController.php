<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:audit-log-list')->only('index');
        $this->middleware('permission:audit-log-show')->only('show');
        $this->middleware('permission:audit-log-export')->only('export');
    }

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $logs = $query->paginate(50);

        return view('admin.pages.audit-logs.index', compact('logs'));
    }

    public function show(string $id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('admin.pages.audit-logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $logs = $this->buildFilteredQuery($request)->limit(10000)->get();

        return Excel::download(new class($logs) implements FromCollection, WithHeadings, WithMapping, WithStyles {
            public function __construct(private $logs) {}

            public function collection()
            {
                return $this->logs;
            }

            public function headings(): array
            {
                return [
                    'التاريخ والوقت',
                    'المستخدم',
                    'الإجراء',
                    'نوع النموذج',
                    'معرف النموذج',
                    'الوصف',
                    'المستوى',
                    'عنوان IP',
                    'الرابط',
                ];
            }

            public function map($log): array
            {
                return [
                    $log->created_at->format('Y-m-d H:i'),
                    $log->user->name ?? 'نظام',
                    $log->action_name_ar,
                    $log->model_type ?? '',
                    $log->model_id ?? '',
                    $log->description ?? '',
                    $log->severity_name_ar,
                    $log->ip_address ?? '',
                    $log->url ?? '',
                ];
            }

            public function styles(Worksheet $sheet): array
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ],
                ];
            }
        }, 'audit-logs-' . date('Y-m-d-His') . '.xlsx');
    }

    /**
     * بناء استعلام سجلات التدقيق مع تطبيق الفلاتر.
     */
    private function buildFilteredQuery(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                  ->orWhere('model_type', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->input('model_type'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        return $query;
    }
}
