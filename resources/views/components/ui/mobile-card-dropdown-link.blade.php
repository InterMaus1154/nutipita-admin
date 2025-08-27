<flux:link
    {{$attributes->merge([
    'class' =>  'no-underline! py-1 px-4 rounded-sm hover:bg-accent/90 hover:text-black cursor-pointer transition-all'
])}}
>
    {{$slot}}
</flux:link>
