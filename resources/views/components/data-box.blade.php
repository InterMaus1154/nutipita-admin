@props(['dataBoxValue', 'dataBoxHeader'])
{{--
<div
    class="flex flex-col justify-between gap-2  border border-gray-200 rounded-xl p-5 text-lg sm:text-xl bg-none dark:border-neutral-700 dark:text-white text-center items-center backdrop-blur-xl bg-accent/10 box-shadow-theme">
    <span>{{$dataBoxHeader}}</span>
    <span>{{$dataBoxValue}}</span>
</div>
--}}
<div class="max-w-xs flex flex-col border border-gray-200 border-t-4 border-t-accent rounded-xl shadow-md/50 shadow-zinc-700 dark:border-neutral-700 dark:border-t-accent dark:bg-linear-to-br from-neutral-950 to-zinc-900">
    <div class="p-4 sm:p-6 bg-zinc-300/5 rounded-xl h-full">
        <h3 class="text-base sm:text-lg text-gray-800 dark:text-white text-center">
            {{$dataBoxHeader}}
        </h3>
        <p class="mt-2 text-gray-500 dark:text-neutral-200 text-center text-xl sm:text-xl font-bold">
            {{$dataBoxValue}}
        </p>
    </div>
</div>
