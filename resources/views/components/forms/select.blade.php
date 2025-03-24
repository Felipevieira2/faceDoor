<div class="mb-1">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium  mb-1">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
            @if(isset($xbindrequired)) <span x-show="{{ $xbindrequired }}" class="text-red-500">*</span> @endif
        </label>
    @endif
    
    <select 
        name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
        id="{{ $id }}" 
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($xbindrequired) x-bind:required="{{ $xbindrequired }}" @endif
        @if($xmodel) x-model="{{ $xmodel }}" @endif
        class="w-full px-3 py-1 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus-visible:outline-none transition-colors duration-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 {{ $error ? 'border-red-500' : 'border-gray-300' }} {{ $class }}"
    >
        {{ $slot }}
    </select>
    
    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

 
</div>