@if (session('success'))
    <div id="global-success-alert"
         x-data="{ show: true }"
         x-show="show"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-6 animate-fade-once">
        
        <div class="bg-green-50 border-4 border-green-600 rounded-3xl p-6 shadow-xl relative overflow-hidden cursor-pointer" @click="show = false">
            <!-- Decorative Background Element -->
            <div class="absolute -right-10 -top-10 text-green-100 opacity-50 rotate-12 pointer-events-none">
                <span class="text-9xl">✨</span>
            </div>

            <!-- Close Button -->
            <button @click.stop="show = false" class="absolute top-4 left-4 text-green-400 hover:text-green-700 transition w-10 h-10 rounded-full bg-white/50 flex items-center justify-center text-2xl font-black shadow-sm z-10">
                &times;
            </button>
            
            <div class="relative flex flex-col md:flex-row items-center gap-6">
                <!-- Big Success Icon -->
                <div class="bg-green-600 text-white p-4 rounded-2xl shadow-lg shrink-0 scale-110 md:scale-125">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div class="text-center md:text-right flex-1">
                    <h3 class="text-2xl md:text-3xl font-black text-green-900 mb-2">
                        🎉 تم بنجاح!
                    </h3>
                    <p class="text-green-700 text-lg md:text-xl font-bold">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    @keyframes fade-once {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-once {
        animation: fade-once 0.5s ease-out forwards;
    }
</style>
