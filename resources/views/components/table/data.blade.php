@props(['class' => '', 'link' => false])
<td class="px-2 py-2 text-sm font-medium text-gray-800 dark:text-neutral-200 {{$link ? 'space-x-2' : ''}} {{$class}}" {{$attributes}}>
    {{$slot}}
</td>
