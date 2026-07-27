{{-- منوی کشویی کارمندان --}}
<li class="nav-item">
    <a class="nav-link text-white d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#employeesMenu" role="button">
        <span><i class="fas fa-user-tie ms-2"></i> کارمندان</span>
        <i class="fas fa-chevron-down small"></i>
    </a>
    <div class="collapse" id="employeesMenu">
        <ul class="nav flex-column ps-3">
            <li class="nav-item"><a href="{{ route('school.employees.index') }}" class="nav-link text-white-50 small py-1">لیست کارمندان</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.create') }}" class="nav-link text-white-50 small py-1">ثبت کارمند جدید</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.index', ['role_id' => 'management']) }}" class="nav-link text-white-50 small py-1">مدیران</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.index', ['role_id' => 'teaching']) }}" class="nav-link text-white-50 small py-1">معلمان</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.index', ['role_id' => 'administrative']) }}" class="nav-link text-white-50 small py-1">اداری</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.index', ['role_id' => 'service']) }}" class="nav-link text-white-50 small py-1">خدماتی</a></li>
            <li class="nav-item"><a href="{{ route('school.employees.report') }}" class="nav-link text-white-50 small py-1">گزارشات</a></li>
        </ul>
    </div>
</li>
