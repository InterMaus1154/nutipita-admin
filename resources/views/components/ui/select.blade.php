@props([
    'options' => [],
    'id'  => null,
    'name' => 'null',
    'width' => 'w-[200px]',
    'showMax' => 5,
    'innerClass' => '',
    'wireModel' => null,
    'showSelected' => false,
    'onChange' => null,
    'onChangeParam' => null
])

@php
    // Get initial value from options or empty string
    $initialValue = $options[0]['key'] ?? '';
@endphp

<div class="relative w-full"
     x-data="{
        open: false,
        selected: @js($initialValue),
        options: @js($options),

        init() {
            @if($wireModel)
            // if the alpine variable is changed, send the changes to livewire
                this.$watch('selected', value => {
                    @this.set('{{ $wireModel }}', value);
                });

                // watch for changes in the livewire value (in a livewire component)
                this.$wire.$watch('{{ $wireModel }}', value => {
                    if (this.selected !== value) {
                        this.selected = value;
                    }
                });

            @endif
        },

        setLivewire(value) {
            this.selected = value;
            @if($onChange)
                @if($onChangeParam)
                    @this.call('{{$onChange}}', value, @js($onChangeParam));
                @else
                    @this.call('{{$onChange}}', value);
                @endif
            @endif
        }
    }">

    {{-- Hidden input --}}
    <input type="hidden" id="{{$id}}" name="{{$name}}" x-model="selected">

    {{-- Wrapper --}}
    <div class="{{$width ? $width.' ' : ''}}bg-zinc-600 w-30 text-center rounded-xl">

        {{-- First option display --}}
        <div class="{{ $innerClass ? $innerClass.' ' : '' }} bg-zinc-800 text-white flex items-center justify-between w-full px-4 py-2 cursor-pointer border-2 border-zinc-500 rounded-xl transition-all duration-300 ease"
             x-on:click="open = !open"
             :class="open === true ? 'shadow-[0_0_10px_1px_var(--color-accent)]' : ''">

            <div class="cursor-pointer" x-text="options.find(o => o.key === selected)?.value"></div>

            <span :class="open ? 'rotate-180 ' : 'rotate-0 '"
                  class="inline-block transition-transform duration-300 ease-in-out">▾</span>
        </div>

        {{-- Options dropdown --}}
        <div style="max-height: {{$showMax * 50}}px"
             class="overflow-y-scroll absolute min-w-full top-full left-0 bg-zinc-800/80 backdrop-blur-lg border-2 border-zinc-500 rounded-xl flex flex-col gap-3 z-10"
             x-show="open"
             x-transition
             x-cloak
             x-on:click.outside="open = false">

            @foreach($options as $option)
                <div :class="@js($showSelected) && selected === @js($option['key']) ? 'bg-zinc-600' : ''"
                     class="text-white hover:bg-accent hover:text-black transition-colors duration-200 cursor-pointer px-4 py-2 w-full rounded-xl"
                     x-on:click="setLivewire(@js($option['key'])); open = false">
                    {{$option['value']}}
                </div>
            @endforeach
        </div>
    </div>
</div>
