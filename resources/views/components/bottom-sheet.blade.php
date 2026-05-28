@props(['id', 'title', 'subtitle' => null])

<div id="{{ $id }}" {{ $attributes->merge(['class' => 'fixed inset-0 z-50 overflow-hidden']) }} x-data="{ open: false }" x-show="open" x-cloak>
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px]" @click="open = false"></div>
    
    <!-- Sheet -->
    <div class="absolute bottom-0 left-0 right-0 bg-white border-t border-black rounded-t-[50px] shadow-2xl transform transition-transform duration-300"
         x-show="open"
         x-transition:enter="translate-y-full"
         x-transition:enter-end="translate-y-0"
         x-transition:leave="translate-y-0"
         x-transition:leave-end="translate-y-full">
        
        <div class="p-8 pb-12">
            <!-- Handle -->
            <div class="w-[102px] h-[6px] bg-[#d9d9d9] rounded-full mx-auto mb-8"></div>
            
            <h2 class="text-[22.5px] font-bold text-black text-center mb-1 leading-tight">{{ $title }}</h2>
            @if($subtitle)
                <p class="text-[13.1px] text-black text-center mb-8">{{ $subtitle }}</p>
            @endif
            
            <div class="mt-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
