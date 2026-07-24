<x-guest-layout>
    <div class="mb-7">
        <p class="page-eyebrow">Selamat Datang</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-950">Masuk ke Kurti SAI</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun terdaftar untuk mengakses dashboard.</p>
    </div>
    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="auth-field" placeholder="nama@email.com">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-sm font-semibold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Lupa password?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required autocomplete="current-password" class="auth-field" placeholder="Masukkan password">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
            Ingat saya
        </label>
        <button type="submit" class="btn-primary w-full py-3">Masuk</button>
    </form>
</x-guest-layout>
