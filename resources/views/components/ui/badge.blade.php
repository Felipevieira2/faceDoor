<span class="inline-flex items-center rounded-full {{ 
    $variant === 'primary' ? 'bg-blue-100 text-blue-800' : '' }}{{ 
    $variant === 'secondary' ? 'bg-gray-100 text-gray-800' : '' }}{{ 
    $variant === 'success' ? 'bg-green-100 text-green-800' : '' }}{{ 
    $variant === 'danger' ? 'bg-red-100 text-red-800' : '' }}{{ 
    $variant === 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}{{ 
    $variant === 'info' ? 'bg-indigo-100 text-indigo-800' : '' }}{{ 
    $size === 'sm' ? 'px-2 py-0.5 text-xs' : '' }}{{ 
    $size === 'md' ? 'px-2.5 py-0.5 text-sm' : '' }}{{ 
    $size === 'lg' ? 'px-3 py-1 text-base' : '' }} {{ 
    $class }}">
    {{ $slot }}
</span> 