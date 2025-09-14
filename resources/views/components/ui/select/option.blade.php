@props([
    'value' => '',
    'text' => '',
    'wireKey' => ''
])
<li wire:key="{{$wireKey}}" data-value="{{$value}}" x-on:click="open = false; selected = '{{$value}}'"
    class="cursor-pointer rounded-2xl py-2 text-center sm:hover:font-bold sm:mx-2 transition-all duration-300 outline-2 outline-transparent sm:hover:outline-accent sm:hover:bg-black/20">
    {{\Illuminate\Support\Str::limit($text, 18)}}
</li>
