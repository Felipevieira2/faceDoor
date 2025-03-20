@props([
    'type' => 'text',
    'name',
    'label' => null,
    'value' => null,
    'error' => null
])

<div class="">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium dark:text-white mb-1">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ $value ?? '' }}" 
        placeholder="{{ $placeholder }}" 
        @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @if($required) required @endif
        class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus-visible:outline-none transition-colors duration-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 {{ $error ? 'border-red-500' : 'border-gray-300' }} {{ $class }}"
    >
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif
</div>