@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('Login') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sistema de Gestão de Acesso de Condomínio
            </p>
        </div>
        
        <div class="mt-8 bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        {{ __('Email') }}
                    </label>
                    <div class="mt-1">
                        <x-forms.input
                            type="email"
                            name="email"
                            id="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="email"
                            :error="$errors->first('email')"
                        />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('Senha') }}
                    </label>
                    <div class="mt-1">
                        <x-forms.input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            :error="$errors->first('password')"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            {{ __('Lembrar-me') }}
                        </label>
                    </div>

                    @if (Route::has('password.request'))
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            {{ __('Esqueceu sua senha?') }}
                        </a>
                    </div>
                    @endif
                </div>

                <div>
                    <x-forms.button type="submit" variant="primary" class="w-full">
                        {{ __('Entrar') }}
                    </x-forms.button>
                </div>

                <div class="text-center mt-4">
                    <p class="text-sm text-gray-600">
                        Não tem uma conta? 
                        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                            Registre-se
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
