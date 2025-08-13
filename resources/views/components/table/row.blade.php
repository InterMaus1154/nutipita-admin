<tr
    {{$attributes->merge([
        'class'=>"odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-accent/10 text-center"
    ])}}
>
    {{$slot}}
</tr>
