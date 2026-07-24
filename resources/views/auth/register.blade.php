<x-guest-layout>
    <div class="mb-7">
        <p class="page-eyebrow">Akun Baru</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-950">Daftar Kurti SAI</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Lengkapi informasi berikut untuk membuat akun.</p>
    </div>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="type" value="Peran" />
            <select id="type" name="type"
                class="field-control mt-1">
                <option value="">Pilih peran</option>
                <option value="orang_tua">
                    Orang Tua
                </option>
                <option value="murid">
                    Murid
                </option>
                <option value="fasil">
                    Fasil
                </option>
            </select>
            <x-input-error :messages="$errors->get('type')" class="mt-2" />

        </div>
        <div class="mt-4">
            <x-input-label for="class" value="Kelas" />
            <select id="classroom_id" name="classroom_id"
                class="field-control mt-1">
                <option value="">Pilih kelas</option>
                    @foreach($classrooms as $classroom)
                        <option value="{{ $classroom->id }}"
                            {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                            {{ $classroom->name }}
                        </option>
                    @endforeach
            </select>
            <x-input-error :messages="$errors->get('classroom_id')" class="mt-2" />

        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex flex-col-reverse items-center gap-3 sm:flex-row sm:justify-between">
            <a class="text-sm font-semibold text-emerald-700 hover:underline" href="{{ route('login') }}">
                Sudah punya akun?
            </a>

            <x-primary-button class="w-full sm:w-auto">
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
