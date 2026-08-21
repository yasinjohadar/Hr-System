@if ($payrolls->total() > 0)
    عرض {{ $payrolls->firstItem() }} إلى {{ $payrolls->lastItem() }} من {{ $payrolls->total() }} نتيجة
@else
    لا توجد نتائج
@endif
