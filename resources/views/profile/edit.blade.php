<x-app-layout>
    <x-slot name="header">
        <span class="font-bold text-xl text-blue-900 flex items-center gap-2"><span>⚙️</span> الإعدادات</span>
    </x-slot>

    <div class="py-6 space-y-5 max-w-2xl">

        {{-- ========= UPDATE NAME & EMAIL ========= --}}
        <div class="glass-card overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg">👤</div>
                <div>
                    <div class="font-black text-base">معلومات الحساب</div>
                    <div class="text-xs text-blue-100">تعديل الاسم والإيميل</div>
                </div>
            </div>
            <div class="p-6">
                @if(session('status') === 'profile-updated')
                    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-sm flex items-center gap-2">
                        ✅ تم تحديث بيانات الحساب بنجاح
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">الاسم</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                            class="w-full rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition px-4 py-2.5 text-sm font-medium text-gray-800 bg-white/80 shadow-sm"
                            required autofocus autocomplete="name">
                        @error('name')
                            <div class="mt-1.5 text-xs text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                            class="w-full rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 transition px-4 py-2.5 text-sm font-medium text-gray-800 bg-white/80 shadow-sm"
                            required autocomplete="username">
                        @error('email')
                            <div class="mt-1.5 text-xs text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-blue-200 w-full sm:w-auto">
                            💾 حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========= UPDATE PASSWORD ========= --}}
        <div class="glass-card overflow-hidden">
            <div class="bg-gradient-to-r from-violet-500 to-purple-600 text-white px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center text-lg">🔒</div>
                <div>
                    <div class="font-black text-base">تغيير كلمة المرور</div>
                    <div class="text-xs text-purple-100">استخدمي كلمة مرور قوية لحماية حسابك</div>
                </div>
            </div>
            <div class="p-6">
                @if(session('status') === 'password-updated')
                    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl font-bold text-sm flex items-center gap-2">
                        ✅ تم تغيير كلمة المرور بنجاح
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-bold text-gray-700 mb-1.5">كلمة المرور الحالية</label>
                        <input type="password" id="current_password" name="current_password"
                            class="w-full rounded-xl border border-gray-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition px-4 py-2.5 text-sm font-medium text-gray-800 bg-white/80 shadow-sm"
                            autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <div class="mt-1.5 text-xs text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">كلمة المرور الجديدة</label>
                        <input type="password" id="password" name="password"
                            class="w-full rounded-xl border border-gray-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition px-4 py-2.5 text-sm font-medium text-gray-800 bg-white/80 shadow-sm"
                            autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <div class="mt-1.5 text-xs text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full rounded-xl border border-gray-200 focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition px-4 py-2.5 text-sm font-medium text-gray-800 bg-white/80 shadow-sm"
                            autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                            <div class="mt-1.5 text-xs text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="bg-violet-600 hover:bg-violet-700 text-white px-7 py-2.5 rounded-xl text-sm font-bold transition shadow-lg shadow-violet-200 w-full sm:w-auto">
                            🔐 تغيير كلمة المرور
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ========= ACCOUNT INFO CARD ========= --}}
        <div class="glass-card p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-200 to-indigo-300 text-white flex items-center justify-center font-black text-2xl shadow-md border-4 border-white shrink-0">
                {{ mb_substr(Auth::user()->name ?? 'J', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="font-black text-gray-800 text-base">{{ Auth::user()->name }}</div>
                <div class="text-sm text-gray-400 mt-0.5">{{ Auth::user()->email }}</div>
                <div class="text-xs text-blue-500 font-bold mt-1">مديرة الأتيليه</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-red-500 hover:text-red-700 font-bold border border-red-200 hover:border-red-400 bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition whitespace-nowrap">
                    ↩ تسجيل خروج
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
