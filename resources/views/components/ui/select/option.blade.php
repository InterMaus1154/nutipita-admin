@props([
    'value' => '',
    'text' => '',
    'wireKey' => ''
])
@aware(['wireChange', 'wireChangeProp'])
<li
    data-value="{{$value}}"
    x-on:click="open = false; selected = @js($value);"
    class="cursor-pointer rounded-2xl py-[10px] flex justify-center items-center text-center font-medium sm:mx-2 transition-all duration-300 outline-2 outline-transparent sm:hover:outline-accent sm:hover:bg-black/20 will-change-transform translate-z-0 hover:text-shadow-[0_0_0.6px_currentColor,0_0_0.6px_currentColor] leading-none"
    :class="{'text-accent': @js($value) === '',
            'hover:scale-[1.08]' : @js($value) !== ''
    }"
>
    {{ $text }}
</li>
