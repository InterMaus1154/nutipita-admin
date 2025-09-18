@props([
    'value' => '',
    'text' => '',
    'wireKey' => ''
])
@aware(['wireChange', 'wireChangeProp'])
<li
    data-value="{{$value}}"
    x-on:click="open = false; selected = @js($value);"
    class="cursor-pointer rounded-2xl py-2 text-center font-medium hover:scale-110 sm:mx-2 transition-all duration-300 outline-2 outline-transparent sm:hover:outline-accent sm:hover:bg-black/20">
    {{ \Illuminate\Support\Str::limit($text, 18) }}
</li>
