<div class="mb-4 flex items-start">
    <div class="flex items-center h-5">
        <input 
            type="checkbox" 
            name="{{ $name }}" 
            id="{{ $id }}" 
            @if(isset($xmodel)) x-model="{{ $xmodel }}" @endif
            @if(isset($xbindrequired)) x-bindrequired="{{ $xbindrequired }}" @endif
            @if($required) required @endif
            value="{{ $value }}" 
            @if($checked) checked @endif
            class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 {{ $class }}"
        >
    </div>
    
    <div class="ml-3 text-sm">
        @if($label)
            <label for="{{ $id }}" class="font-medium ">{{ $label }}</label>
        @endif
        
        @if($error)
            <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
        @endif
    </div>
</div>