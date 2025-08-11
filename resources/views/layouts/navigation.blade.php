<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 p-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">

        {{-- Logo / Brand --}}
        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-gray-200">
            UMKM Dashboard
        </a>

        {{-- Navigation Links --}}
        <div class="space-x-6 flex items-center">
            <a href="{{ route('dashboard') }}" class="text-gray-700 dark:text-gray-300 hover:underline">
                Dashboard
            </a>
            <a href="{{ route('umkm.index') }}" class="text-gray-700 dark:text-gray-300 hover:underline">
                Data UMKM
            </a>
            <a href="{{ route('kategori.index') }}" class="text-gray-700 dark:text-gray-300 hover:underline">
                Kategori dan Sektor Usaha
            </a>

            {{-- Logout Form --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:underline">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
