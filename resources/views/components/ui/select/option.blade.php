@props([
    'value' => '',
    'text' => '',
    'wireKey' => ''
])
@aware(['wireChange', 'wireChangeProp'])
<li
    data-value="{{$value}}"
    x-on:click="open = false; selected = @js($value);"
    class="cursor-pointer rounded-2xl min-h-[40px] flex justify-center items-center text-center font-medium hover:scale-[1.08] sm:mx-2 transition-all duration-300 outline-2 outline-transparent sm:hover:outline-accent sm:hover:bg-black/20 will-change-transform translate-z-0 hover:text-shadow-[0_0_0.6px_currentColor,0_0_0.6px_currentColor]"
    :class="{'text-accent': @js($value) === ''}"
>
    {{ $text }}
</li>
