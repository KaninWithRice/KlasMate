@props(['message', 'type' => 'success'])

<div class="fixed top-20 left-1/2 -translate-x-1/2 w-[340px] bg-[#f5c32f] border-[1.5px] border-black rounded-[15px] p-8 shadow-2xl z-50 animate-fade-in-down flex flex-col items-center text-center" 
     x-data="{ show: true }" 
     x-show="show" 
     x-cloak
     x-init="setTimeout(() => show = false, 4000)">
    
    <!-- Close Button -->
    <button @click="show = false" class="absolute top-4 right-4 text-black hover:opacity-70">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Folder Mascot (Prominent as per Figma) -->
    <div class="w-24 h-16 mb-6">
        <img src="{{ asset('images/message-popup.png') }}" alt="Mascot" class="w-full h-full object-contain">
    </div>

    <!-- Message -->
    <h2 class="font-['Poppins'] font-black text-[22px] text-black leading-tight tracking-tight">
        {{ $message }}
    </h2>
</div>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translate(-50%, -20px); }
        100% { opacity: 1; transform: translate(-50%, 0); }
    }
    .animate-fade-in-down {
        animation: fade-in-down 0.3s ease-out;
    }
</style>
