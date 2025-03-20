<div class="bg-white shadow rounded-lg overflow-hidden {{ $class }}">
    @if($title)
        <div class="px-4 py-5 sm:px-6  border-gray-200">
            <h3 class="text-lg font-medium leading-6">{{ $title }}</h3>
        </div>
    @endif
    
    <div class="px-4 py-5 sm:p-6">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-4 py-4 sm:px-6 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div> 