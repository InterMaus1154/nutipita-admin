@props(['class' => '', 'link' => false])
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 {{$link ? 'space-x-2' : ''}} {{$class}}" {{$attributes}}>
    {{$slot}}
</td>
