@props(['dataBoxValue', 'dataBoxHeader'])
<div
    class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white text-center items-center text-xl">
    <span>{{$dataBoxHeader}}</span>
    <span>{{$dataBoxValue}}</span>
</div>
