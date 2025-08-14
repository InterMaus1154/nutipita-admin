@props(['class' => '', 'link' => false])
<td class="px-2 py-4 text-sm font-medium text-gray-800 dark:text-neutral-200 {{$link ? 'whitespace-nowrap space-x-1' : ''}} {{$class}}" {{$attributes}}>
    {{$slot}}
</td>
