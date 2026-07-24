<x-guest-layout>
    <div class="mb-7">
        <p class="page-eyebrow">Pemulihan Akun</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-950">Lupa Password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Masukkan email akun. Kami akan mengirimkan tautan untuk membuat password baru.</p>
    </div>
    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-700">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="auth-field" placeholder="nama@email.com">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <button type="submit" class="btn-primary w-full py-3">Kirim Link Reset</button>
    </form>
    <div class="mt-6 text-center"><a href="{{ route('login') }}" class="text-sm font-semibold text-emerald-700 hover:underline">← Kembali ke login</a></div>
</x-guest-layout>
