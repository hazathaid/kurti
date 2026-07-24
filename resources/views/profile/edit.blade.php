<x-app-layout>
    <x-slot name="header">
        <p class="page-eyebrow">Pengaturan Akun</p>
        <h1 class="page-title">Profil</h1>
        <p class="page-description">Kelola data pribadi, password, dan keamanan akun.</p>
    </x-slot>

    <div class="pb-8">
        <div class="mx-auto max-w-7xl space-y-6">
            <div class="surface p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="surface p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="surface border-rose-200 p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
