<li>
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
        <svg class="{{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
        Dashboard
    </a>
</li>

@auth
    @if(auth()->user()->role?->name === 'Admin TU')
    <li>
        <div class="text-xs font-semibold leading-6 text-gray-400 mt-4 mb-2">Administrasi</div>
        <ul role="list" class="-mx-2 space-y-1">
            <li>
                <a href="{{ route('admin.panel') }}" class="{{ request()->routeIs('admin.panel') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->routeIs('admin.panel') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" />
                    </svg>
                    Panel Admin
                </a>
            </li>
             <li>
                <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->routeIs('admin.students.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Data Siswa
                </a>
            </li>
             <li>
                <a href="{{ route('admin.teachers.index') }}" class="{{ request()->routeIs('admin.teachers.*') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->routeIs('admin.teachers.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Data Guru
                </a>
            </li>
             <li>
                <a href="{{ route('admin.classes.index') }}" class="{{ request()->routeIs('admin.classes.*') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->routeIs('admin.classes.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    Data Kelas
                </a>
            </li>
            <li>
                <a href="{{ route('admin.subjects.index') }}" class="{{ request()->routeIs('admin.subjects.*') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->routeIs('admin.subjects.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    Mata Pelajaran
                </a>
            </li>
            <li>
                <a href="{{ route('admin.attendance') }}" class="{{ request()->is('admin/attendance') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->is('admin/attendance') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5M3.75 9.75h16.5m-16.5 4.5H12m-8.25 4.5h8.25" />
                    </svg>
                    Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.assessments.ui') }}" class="{{ request()->is('admin/assessments/ui') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->is('admin/assessments/ui') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12" />
                    </svg>
                    Penilaian
                </a>
            </li>
            <li>
                <a href="{{ route('admin.report-cards.ui') }}" class="{{ request()->is('admin/report-cards/ui') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->is('admin/report-cards/ui') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7.5l3 3 6-6M4.5 6.75h7.5M4.5 12h4.5M4.5 17.25H9" />
                    </svg>
                    Raport
                </a>
            </li>
            <li>
                <a href="{{ route('admin.announcements') }}" class="{{ request()->is('admin/announcements') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->is('admin/announcements') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 8.25l7.5-3 7.5 3m-15 0l7.5 3 7.5-3m-15 0v6.75l7.5 3m7.5-9.75v6.75l-7.5 3m0-6.75v6.75" />
                    </svg>
                    Pengumuman
                </a>
            </li>
            <li>
                <a href="{{ route('admin.parent-portal') }}" class="{{ request()->is('admin/parent-portal') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                    <svg class="{{ request()->is('admin/parent-portal') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a3 3 0 110 6 3 3 0 010-6zm0 0c3.728 0 6.75 1.567 6.75 3.5S15.728 13.75 12 13.75s-6.75-1.567-6.75-3.5S8.272 6.75 12 6.75zm-6.75 9c0-1.933 3.022-3.5 6.75-3.5s6.75 1.567 6.75 3.5-3.022 3.5-6.75 3.5-6.75-1.567-6.75-3.5z" />
                    </svg>
                    Portal Orang Tua
                </a>
            </li>
        </ul>
    </li>
    @endif
@endauth
