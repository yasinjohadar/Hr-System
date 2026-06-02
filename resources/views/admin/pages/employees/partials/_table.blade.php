<div class="employees-table-wrapper">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="mb-0 text-muted">قائمة الموظفين (<span id="employees-total">{{ $employees->total() }}</span>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 employees-table">
            <thead>
                <tr>
                    <th scope="col" style="width: 48px;">#</th>
                    <th scope="col">رقم الموظف</th>
                    <th scope="col">الاسم</th>
                    <th scope="col">القسم</th>
                    <th scope="col">المنصب</th>
                    <th scope="col">البريد</th>
                    <th scope="col">الهاتف</th>
                    <th scope="col">تاريخ التوظيف</th>
                    <th scope="col" style="width: 130px;">الحالة</th>
                    <th scope="col" class="text-center" style="width: 70px;">العمليات</th>
                </tr>
            </thead>
            <tbody id="employees-table-body">
                @include('admin.pages.employees._index_rows', ['employees' => $employees])
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <small class="text-muted">إجمالي النتائج: <strong id="employees-total-footer">{{ $employees->total() }}</strong></small>
    <div id="employees-pagination">
        @include('admin.pages.employees._index_pagination', ['employees' => $employees])
    </div>
</div>
