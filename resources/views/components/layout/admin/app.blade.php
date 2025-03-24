<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - FaceDoor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-dark-900 dark:bg-gray-100">
    @if ($showHeader)
        <header class="bg-white dark:bg-gray-800 shadow-md sticky top-0 z-50 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('login') }}"
                                class="text-2xl font-bold text-primary-600 dark:text-primary-400 transition duration-300 hover:text-primary-800 dark:hover:text-primary-300">FaceDoor</a>
                        </div>


                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('admin.dashboard') }}"
                                class="{{ request()->routeIs('admin.dashboard') ? 'border-primary-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200">
                                Início
                            </a>
                            @auth

                                {{-- <div class="ml-3 relative">
                                    <div>
                                        <button type="button"
                                            class="bg-white dark:bg-gray-800 rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800"
                                            id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                            <span class="sr-only">Abrir menu do usuário</span>
                                            Cadastro
                                        </button>
                                    </div>
                                    <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                                        role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                        tabindex="-1" id="user-menu">
                                        <a href="{{ route('admin.moradores.index') }}"
                                            class="{{ request()->routeIs('admin.moradores.*') ? 'border-primary-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200">
                                            Moradores
                                        </a>
                                        <a href="{{ route('admin.dispositivos.index') }}"
                                            class="{{ request()->routeIs('admin.dispositivos.*') ? 'border-primary-500 text-gray-900 dark:text-white' : 'border-transparent text-gray-500 dark:text-gray-300 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-200">
                                            Dispositivos
                                        </a>
                                    </div>
                                </div> --}}
                                <div class="relative cursor-pointer" x-data="{ open: false }">
                                    <button @click="open = !open" type="button"
                                        class="cursor-pointer flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700">
                                        <i class="fas fa-users-cog mr-2"></i>
                                        <span>Cadastro</span>
                                        <svg class="w-5 h-5 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak @click.outside="open = false"
                                        class="absolute right-0 z-10 mt-2 w-42 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                        <div class="flex flex-col ">
                                            <a href="{{ route('admin.moradores.index') }}"
                                                class=" px-2 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 flex items-center ">
                                                <i class="fas fa-users mr-1"></i>Moradores
                                            </a>
                                            <a href="{{ route('admin.visitantes.index') }}"
                                                class=" px-2 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 flex items-center ">
                                                <i class="fa-solid fa-user-clock mr-2"></i>Visitantes
                                            </a>
                                            <a href="{{ route('admin.dispositivos.index') }}"
                                            class=" px-2 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 flex items-center ">
                                            <i class="fa-solid fa-tablet-screen-button mr-2"></i>Dispositivos
                                        </a>

                                        </div>
                                    </div>
                                </div>

                            @endauth
                        </div>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        {{-- <!-- Seletor de Temas -->
                        <div class="mr-3">
                            <x-theme-switcher />
                        </div> --}}

                        @auth
                            <button type="button"
                                class="p-1 rounded-full text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-colors duration-200 cursor-pointer">
                                <span class="sr-only">Ver notificações</span>
                                <i class="fas fa-bell"></i>
                            </button>
                            <div class="ml-3 relative">
                                <div>
                                    <button type="button"
                                        class="bg-white dark:bg-gray-800 rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800"
                                        id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Abrir menu do usuário</span>
                                        <img class="h-8 w-8 rounded-full"
                                            src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=7F9CF5&background=EBF4FF"
                                            alt="{{ Auth::user()->name }}">
                                    </button>
                                </div>
                                <div class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                                    role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button"
                                    tabindex="-1" id="user-menu">
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                        role="menuitem" tabindex="-1">
                                        Seu Perfil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">
                                            Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition duration-200">Entrar</a>
                            <a href="{{ route('register') }}"
                                class="ml-4 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition duration-200">Registrar</a>
                        @endauth
                    </div>
                    <div class="-mr-2 flex items-center sm:hidden">
                        <!-- Botão de alternância de tema para mobile -->
                        {{-- <div class="mr-2">
                            <x-theme-switcher />
                        </div> --}}

                        <button type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition duration-200"
                            id="mobile-menu-button">
                            <span class="sr-only">Abrir menu</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu Mobile -->
            <div class="hidden sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard')
                            ? 'bg-primary-50 dark:bg-primary-900 border-primary-500 text-primary-700 dark:text-primary-300'
                            : 'border-transparent text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} 
                        block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-200">
                        Início
                    </a>
                    @auth
                        <a href="{{ route('admin.moradores.index') }}"
                            class="{{ request()->routeIs('admin.moradores.*')
                                ? 'bg-primary-50 dark:bg-primary-900 border-primary-500 text-primary-700 dark:text-primary-300'
                                : 'border-transparent text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} 
                            block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-200">
                            Moradores
                        </a>
                        <a href="{{ route('admin.dispositivos.index') }}"
                            class="{{ request()->routeIs('admin.dispositivos.*')
                                ? 'bg-primary-50 dark:bg-primary-900 border-primary-500 text-primary-700 dark:text-primary-300'
                                : 'border-transparent text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-200' }} 
                            block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-200">
                            Dispositivos
                        </a>
                    @endauth
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                    @auth
                        <div class="flex items-center px-4">
                            <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full"
                                    src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&color=7F9CF5&background=EBF4FF"
                                    alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="ml-3">
                                <div class="text-base font-medium text-gray-800 dark:text-white">{{ Auth::user()->name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="#"
                                class="block px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                                Seu Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                                    Sair
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('login') }}"
                                class="block px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                                Entrar
                            </a>
                            <a href="{{ route('register') }}"
                                class="block px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-200">
                                Registrar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </header>
    @endif

    <main class="py-6 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm dark:bg-green-900/30 dark:border-green-500 dark:text-green-300"
                    role="alert">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm dark:bg-red-900/30 dark:border-red-500 dark:text-red-300"
                    role="alert">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <div class=" ">
                {{ $slot }}
            </div>
        </div>
    </main>

    @if ($showFooter)
        <footer class="bg-dark-800 shadow mt-auto py-4 dark:bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; {{ date('Y') }} FaceDoor - Sistema de Gestão de Acesso de Condomínio
                </div>
            </div>
        </footer>
    @endif
    @stack('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/solid.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
</body>

</html>
