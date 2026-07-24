<x-guest-layout>
    <div class="mb-7">
        <p class="page-eyebrow">Verifikasi Akun</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-950">Cek Email Anda</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Klik tautan verifikasi yang kami kirimkan sebelum mulai menggunakan Kurti SAI.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-700">
            Tautan verifikasi baru telah dikirim ke email Anda.
        </div>
    @endif

    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
                Logout
            </button>
        </form>
    </div>
</x-guest-layout>
