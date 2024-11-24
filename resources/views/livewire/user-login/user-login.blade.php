<div class="flex min-h-screen w-full">
    <div class="w-3/5 sm:w-3/5 hidden sm:block ">
        <img src="{{ asset('images/login.jpg') }}" alt="Descripción de la imagen" class="w-full h-full object-cover">
    </div>
    <div class="flex-1  flex justify-center items-center">
        <div class="min-h-screen flex items-center justify-center  py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full  overflow-hidden sm:rounded-lg sm:overflow-hidden">
                <div class="px-6 py-8">
                    <div class="flex justify-center">
                        <img src="{{ asset('images/logo-1.jpg') }}" width="180px">
                    </div>
                    </svg>
                    <form class="mt-8 space-y-6" wire:submit.prevent="attemptLogin">
                        <div class="rounded-md shadow-sm space-y-4">
                            @if (session()->has('error'))
                                <div class="mb-6">
                                    <span class="text-red-500">{{ session('error') }}</span>
                                </div>
                            @endif
                            <div class="mb-6">
                                <label for="email-address" class="sr-only">Email address</label>
                                <input id="email-address" name="email" type="email" autocomplete="email" required
                                    class="appearance-none rounded-none relative block w-96 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                    placeholder="Correo" wire:model="email">
                                @error('email')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="password" class="sr-only">Password</label>
                                <input id="password" name="password" type="password" autocomplete="current-password"
                                    required
                                    class="appearance-none rounded-none relative block w-96 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                                    placeholder="Contraseña" wire:model="password">
                                @error('password')
                                    <span class="text-red-500">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <button type="submit"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd"
                                            d="M3 4a3 3 0 013-3h8a3 3 0 013 3v8a3 3 0 01-3 3H6a3 3 0 01-3-3V4zm10 2a1 1 0 10-2 0v6a1 1 0 102 0V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                Iniciar sesión
                            </button>
                            <div wire:loading wire:target="attemptLogin">
                                <div class="fixed top-0 left-0 w-full h-full flex items-center justify-center  z-50">
                                    <svg class="animate-spin h-16 w-16 text-blue-600 mr-3"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"
                                            stroke-width="2" stroke="currentColor" fill="none"></circle>
                                        <path d="M12 2a10 10 0 00-9.5 7.5M12 2c2.5 0 5 1 7 2.5" stroke="currentColor"
                                            stroke-width="2" fill="none"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
