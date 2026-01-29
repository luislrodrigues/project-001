<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('API Tokens') }}
        </h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <p class="text-gray-600">Gestiona los tokens de API desde tu perfil en la aplicación.</p>
            <a href="{{ url('/profile') }}" class="inline-flex mt-4 items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Ir al perfil</a>
        </div>
    </div>
</x-app-layout>
