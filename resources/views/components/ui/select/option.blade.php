@props([
    'value' => '',
    'text' => ''
])
<li data-value="{{$value}}" x-on:click="open = false; selected = '{{$value}}'"
    class="cursor-pointer rounded-2xl p-2 text-center hover:font-bold mx-4 transition-all duration-300 outline-2 outline-transparent hover:outline-accent hover:bg-black/20">
    {{\Illuminate\Support\Str::limit($text, 18)}}
</li>
