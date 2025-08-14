<div class="relative min-h-screen w-full flex flex-col justify-center items-center" style="background: url('{{ asset('images/login-1.jpg') }}') no-repeat center center/cover;">
    <div class="absolute inset-0 bg-white bg-opacity-10"></div>
    <div class="relative w-full flex flex-col justify-center items-center min-h-screen">
        <div class="w-full max-w-md min-h-[600px] flex flex-col justify-center items-center border-2 border-white-200 shadow-2xl rounded-2xl bg-white p-8">
                <div class="px-6 py-8">
                    <div class="flex justify-center mb-12 mt-2">
                        <img src="{{ asset('images/logo-1.JPG') }}" width="260px">
                    </div>
                    </svg>
                    <form class="mt-8 space-y-6" wire:submit.prevent="attemptLogin">
                        <div class="rounded-md shadow-sm space-y-12">
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
                            <div class="relative">
                                <div class="relative">
                                    <input type="password" id="password"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 pr-10 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                        placeholder="Contraseña" wire:model="password">
                                    <span id="toggle-password"
                                        class="absolute inset-y-0 right-3 flex items-center pointer-events-auto cursor-pointer text-gray-500 hover:text-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-5 h-5" id="eye-icon">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12c2.11 3.472 5.398 5.25 9 5.25s6.89-1.778 9-5.25c-2.11-3.472-5.398-5.25-9-5.25S4.36 8.528 2.25 12z" />
                                        </svg>
                                    </span>
                                </div>
                                @error('password')
                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div>
                            <button type="submit"
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-lg font-bold rounded-md text-white bg-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                Iniciar sesión
                            </button>
                            <div wire:loading wire:target="attemptLogin,rememberPassword">
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
                    <a class="block text-center text-lg font-semibold underline text-blue-900 hover:text-blue-800 rounded-md mt-8 focus:outline-none focus:ring-2 focus:ring-blue-900 cursor-pointer" wire:click="rememberPassword" >
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>

        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('toggle-password');
        const eyeIcon = document.getElementById('eye-icon');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;

            if (type === 'text') {
                eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223C2.845 9.528 2.25 11.24 2.25 12c2.11 3.472 5.398 5.25 9 5.25a10.923 10.923 0 004.782-1.047" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.35 19.65L19.65 4.35" />
            `;
            } else {
                eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 12c0 2.071-1.679 3.75-3.75 3.75S8.25 14.071 8.25 12s1.679-3.75 3.75-3.75 3.75 1.679 3.75 3.75z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12c2.11 3.472 5.398 5.25 9 5.25s6.89-1.778 9-5.25c-2.11-3.472-5.398-5.25-9-5.25S4.36 8.528 2.25 12z" />
            `;
            }
        });

        Livewire.on('alertSuccess', (message) => {
            alertSuccess(message);
        });

        Livewire.on('alertFailed', (message) => {
            alertFailed(message);
        });
    </script>
@endpush
