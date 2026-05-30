@props(['isSidebar' => false])

@if($isSidebar)
    <div class="flex flex-col h-full p-8">
        <div class="flex items-center space-x-3 mb-12">
            <div class="w-10 h-10 bg-[#f5c32f] rounded-lg flex items-center justify-center border border-black shadow-sm">
                <span class="font-black text-xl">K</span>
            </div>
            <span class="font-black text-2xl tracking-tighter">KlasMate</span>
        </div>

        <nav class="flex-1 space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-4 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#072ac6] text-white shadow-lg' : 'text-[#787878] hover:bg-gray-50' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                <span class="font-bold">Courses</span>
            </a>

            <a href="{{ route('notifications') }}" class="flex items-center space-x-4 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('notifications') ? 'bg-[#072ac6] text-white shadow-lg' : 'text-[#787878] hover:bg-gray-50' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                <span class="font-bold">Notifications</span>
            </a>

            <a href="{{ route('friends') }}" class="flex items-center space-x-4 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('friends') ? 'bg-[#072ac6] text-white shadow-lg' : 'text-[#787878] hover:bg-gray-50' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                <span class="font-bold">Friends</span>
            </a>

            <a href="{{ route('settings') }}" class="flex items-center space-x-4 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('settings') ? 'bg-[#072ac6] text-white shadow-lg' : 'text-[#787878] hover:bg-gray-50' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                <span class="font-bold">Settings</span>
            </a>
        </nav>

        <div class="mt-auto pt-8 border-t border-black/10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-[#f5c32f] rounded-full border border-black overflow-hidden">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center font-bold text-black">{{ substr(auth()->user()->name, 0, 1) }}</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-black truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[#787878] truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[#787878]/30 h-[66px] flex items-center justify-around px-4 z-40">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('dashboard') ? 'text-[#072ac6]' : 'text-[#787878]' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/>
            </svg>
            <span class="text-[11px] font-medium">Courses</span>
        </a>

        <a href="{{ route('notifications') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('notifications') ? 'text-[#072ac6]' : 'text-[#787878]' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
            </svg>
            <span class="text-[11px] font-medium">Notifications</span>
        </a>

        <a href="{{ route('friends') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('friends') ? 'text-[#072ac6]' : 'text-[#787878]' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5s-3 1.34-3 3 1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
            </svg>
            <span class="text-[11px] font-medium">Friends</span>
        </a>

        <a href="{{ route('settings') }}" class="flex flex-col items-center space-y-1 {{ request()->routeIs('settings') ? 'text-[#072ac6]' : 'text-[#787878]' }}">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
            <span class="text-[11px] font-medium">Settings</span>
        </a>
    </div>
@endif
