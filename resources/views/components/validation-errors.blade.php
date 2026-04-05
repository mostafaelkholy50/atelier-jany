@if ($errors->any())
    <div id="global-error-alert" 
         x-data="{ show: true }" 
         x-show="show" 
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-6 animate-bounce-subtle sticky top-20 z-[60]">
        
        <div class="bg-red-50 border-4 border-red-600 rounded-3xl p-6 shadow-[0_20px_50px_rgba(220,38,38,0.3)] relative overflow-hidden cursor-pointer" @click="show = false">
            <!-- Decorative Background Element -->
            <div class="absolute -right-10 -top-10 text-red-100 opacity-50 rotate-12 pointer-events-none">
                <span class="text-9xl">⚠️</span>
            </div>

            <!-- Close Button -->
            <button @click.stop="show = false" class="absolute top-4 left-4 text-red-400 hover:text-red-700 transition w-10 h-10 rounded-full bg-white/50 flex items-center justify-center text-2xl font-black shadow-sm z-10">
                &times;
            </button>
            
            <div class="relative flex flex-col md:flex-row items-center gap-6">
                <!-- Big Warning Icon -->
                <div class="bg-red-600 text-white p-4 rounded-2xl shadow-lg shrink-0 scale-110 md:scale-125">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 md:h-12 md:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 17c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <div class="text-center md:text-right flex-1">
                    <h3 class="text-2xl md:text-3xl font-black text-red-900 mb-2">
                        ⚠️ خلي بالك.. الطلب "مضافش" لسه!
                    </h3>
                    <p class="text-red-700 text-lg md:text-xl font-bold">
                        فيه مشكلة في البيانات اللي دخلتيها، راجعي الخانات اللي باللون الأحمر وصلحيها عشان تقدري تحفظي الطلب.
                    </p>
                </div>

                <!-- Action Shortcut -->
                <div class="shrink-0 hidden lg:block">
                    <div class="bg-red-100 text-red-700 px-6 py-4 rounded-2xl font-black text-center border-2 border-red-200">
                        <p class="text-sm mb-1 uppercase tracking-wider opacity-60">تنبيه هام</p>
                        <p class="text-xl">راجعي البيانات!</p>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Sticky-like attention grabber -->
            <div class="mt-4 p-3 bg-red-100 rounded-xl text-center border border-red-200 md:hidden">
                <span class="text-red-700 font-black">البيانات لم تُحفظ ❌ ارجعي شوفيها (اضغطي للإخلاء)</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smoothly scroll to the error alert on mount
            const errorAlert = document.getElementById('global-error-alert');
            if (errorAlert) {
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>

    <style>
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite;
        }
    </style>
@endif
