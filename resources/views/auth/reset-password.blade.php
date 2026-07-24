<x-guest-layout>
    <div class="mb-7">
        <p class="page-eyebrow">Keamanan Akun</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-950">Buat Password Baru</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan password yang kuat dan berbeda dari password sebelumnya.</p>
    </div>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div>
            <label for="email" class="field-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus class="auth-field">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="field-label">Password Baru</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="auth-field">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="field-label">Konfirmasi Password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="auth-field">
        </div>
        <button type="submit" class="btn-primary w-full py-3">Simpan Password Baru</button>
    </form>
</x-guest-layout>
