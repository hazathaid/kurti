<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
  <div class="w-full max-w-md bg-gray-800 rounded-2xl shadow-lg p-6">
    <h1 class="text-2xl font-bold text-white mb-1">Masuk</h1>
    <p class="text-sm text-gray-400 mb-6">Silakan login untuk melanjutkan</p>

    @if (session('status'))
      <div class="mb-4 text-sm text-green-400">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
      @csrf

      {{-- Email --}}
      <div>
        <label for="email" class="block text-sm text-gray-300 mb-1">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email"
               class="w-full rounded-lg border border-gray-600 bg-gray-900 px-3 py-2 text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        @error('email')
          <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm text-gray-300 mb-1">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password"
               class="w-full rounded-lg border border-gray-600 bg-gray-900 px-3 py-2 text-white placeholder-gray-500 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        @error('password')
          <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
        @enderror
      </div>

      {{-- Remember me & Forgot --}}
      <div class="flex items-center justify-between">
        <label class="flex items-center gap-2 text-sm text-gray-400">
          <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-indigo-500 focus:ring-indigo-400">
          Remember me
        </label>

        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-sm text-indigo-400 hover:underline">
            Lupa password?
          </a>
        @endif
      </div>

      {{-- Submit --}}
      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 rounded-lg shadow-md transition">
        Login
      </button>
    </form>
  </div>
</body>
</html>
