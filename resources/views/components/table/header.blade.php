@props(['sortField' => null])
<th {{$attributes->merge([
    'class' => "px-2 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500",
    'scope' => "col"
])}}
    @if($sortField)
        wire:click="setSort('{{$sortField}}')"
    @endif
>
    @if($sortField)
        <div class="flex items-center justify-center text-center cursor-pointer">
            {{$slot}}
            <flux:icon.arrows-up-down variant="mini"/>
        </div>
    @else
        {{$slot}}
    @endif
</th>
