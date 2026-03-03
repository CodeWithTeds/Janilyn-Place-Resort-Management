<div class="mb-6 border-b border-gray-200">
    <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
        <a href="{{ route('owner.staff-management.index') }}" 
           class="{{ request()->routeIs('owner.staff-management.index') ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Staff List
        </a>
        <a href="{{ route('owner.staff-management.schedules') }}" 
           class="{{ request()->routeIs('owner.staff-management.schedules') ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Schedules
        </a>
        <a href="{{ route('owner.staff-management.tasks') }}" 
           class="{{ request()->routeIs('owner.staff-management.tasks') ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Tasks
        </a>
        <a href="{{ route('owner.staff-management.attendance') }}" 
           class="{{ request()->routeIs('owner.staff-management.attendance') ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Attendance
        </a>
        <a href="{{ route('owner.staff-management.performance') }}" 
           class="{{ request()->routeIs('owner.staff-management.performance') ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
            Performance
        </a>
    </nav>
</div>
