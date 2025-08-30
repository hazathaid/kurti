<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-gray-800 rounded-2xl shadow-lg p-6">
    <h1 class="text-2xl font-bold text-white mb-1">Reset Password</h1>
    <p class="text-sm text-gray-400 mb-6">
      Silakan masukkan password baru untuk akunmu.
    </p>

    {{-- Status pesan (jika ada) --}}
    @if (session('status'))
      <div class="mb-4 text-sm text-green-400">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
      @csrf

      {{-- Token reset password (wajib) --}}
      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      {{-- Email --}}
      <div>
        <label for="email" class="block text-sm text-gray-300 mb-1">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus
               class="w-full rounded-lg border border-gray-600 bg-gray-900 px-3 py-2 text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        @error('email')
          <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
      </div>

      {{-- Password baru --}}
      <div>
        <label for="password" class="block text-sm text-gray-300 mb-1">Password Baru</label>
        <input id="password" name="password" type="password" required autocomplete="new-password"
               class="w-full rounded-lg border border-gray-600 bg-gray-900 px-3 py-2 text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        @error('password')
          <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
      </div>

      {{-- Konfirmasi password --}}
      <div>
        <label for="password_confirmation" class="block text-sm text-gray-300 mb-1">Konfirmasi Password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
               class="w-full rounded-lg border border-gray-600 bg-gray-900 px-3 py-2 text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      {{-- Tombol submit --}}
      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 rounded-lg shadow-md transition">
        Simpan Password Baru
      </button>
    </form>

    <div class="mt-6 text-center">
      <a href="{{ route('login') }}" class="text-sm text-indigo-400 hover:underline">
        Kembali ke Login
      </a>
    </div>
  </div>
</body>
</html>
