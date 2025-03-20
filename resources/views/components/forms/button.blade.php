<button 
    type="{{ $type }}" 
    @if($disabled) disabled @endif
    {{ $attributes->merge([
        'class' => 'cursor-pointer inline-flex items-center justify-center rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 ' . 
        ($disabled ? 'opacity-50 cursor-not-allowed ' : '') .
        ($variant === 'primary' ? 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500 ' : '') .
        ($variant === 'secondary' ? 'bg-gray-200 hover:bg-gray-300 text-gray-700 focus:ring-gray-500 ' : '') .
        ($variant === 'danger' ? 'bg-red-500 hover:bg-red-600 text-white focus:ring-red-400 ' : '') .
        ($variant === 'success' ? 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500 ' : '') .
        ($variant === 'warning' ? 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-500 ' : '') .
        ($size === 'sm' ? 'px-2.5 py-1.5 text-xs ' : '') .
        ($size === 'md' ? 'px-4 py-2 text-sm ' : '') .
        ($size === 'lg' ? 'px-6 py-3 text-base ' : '') .
        $class
    ]) }}
>
    {{ $slot }}
</button>