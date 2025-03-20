<div 
    x-data="{ open: false }" 
    x-show="open" 
    x-on:open-modal.window="if ($event.detail.id === '{{ $id }}') open = true" 
    x-on:close-modal.window="if ($event.detail.id === '{{ $id }}') open = false" 
    x-on:keydown.escape.window="open = false" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto" 
    style="display: none;"
>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
            aria-hidden="true"
            x-on:click="open = false"
        ></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle {{ $size === 'sm' ? 'sm:max-w-sm' : '' }} {{ $size === 'md' ? 'sm:max-w-lg' : '' }} {{ $size === 'lg' ? 'sm:max-w-2xl' : '' }} {{ $size === 'xl' ? 'sm:max-w-4xl' : '' }} {{ $size === '2xl' ? 'sm:max-w-6xl' : '' }} sm:w-full sm:p-6 {{ $class }}"
            role="dialog" 
            aria-modal="true" 
            aria-labelledby="modal-headline"
            x-on:click.away="open = false"
        >
            @if($title)
                <div class="mb-4">
                    <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-headline">
                        {{ $title }}
                    </h3>
                </div>
            @endif

            <div>
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="mt-5 sm:mt-6">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div> 