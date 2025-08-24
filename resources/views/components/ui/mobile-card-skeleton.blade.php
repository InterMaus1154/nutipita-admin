<div
    {{$attributes->merge([
        'class' => 'flex flex-col gap-4 rounded-sm shadow-sm border-1 border-neutral-700 p-4',
        'x-data' => '{actionMenuOpen: false}'
    ])}}
>
    {{$slot}}
</div>
