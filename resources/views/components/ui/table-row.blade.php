<tr class="hover:bg-gray-50 {{ $class }}">
    {{ $slot }}
    
    @if(isset($actions))
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            {{ $actions }}
        </td>
    @endif
</tr> 