@props(['dataBoxValue', 'dataBoxHeader'])
<div
    class="flex flex-col justify-between gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-5 text-lg sm:text-xl dark:bg-neutral-900 dark:border-neutral-700 dark:text-white text-center items-center ">
    <span>{{$dataBoxHeader}}</span>
    <span>{{$dataBoxValue}}</span>
</div>
