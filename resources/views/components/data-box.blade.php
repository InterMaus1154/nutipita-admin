@props(['dataBoxValue', 'dataBoxHeader', 'subtitle' => ''])

{{--<div--}}
{{--    class="flex flex-col justify-between gap-2  border border-gray-200 rounded-xl p-5 text-lg sm:text-xl bg-none dark:border-neutral-700 dark:text-white text-center items-center backdrop-blur-xl  box-shadow-theme">--}}
{{--    <span>{{$dataBoxHeader}}</span>--}}
{{--    <span>{{$dataBoxValue}}</span>--}}
{{--</div>--}}

<div class="flex flex-col border-gray-200 border-1  rounded-xl shadow-md/50  dark:border-zinc-800 dark:bg-linear-to-br from-neutral-950 to-zinc-900">
    <div class="flex flex-col justify-between p-2 py-4 sm:p-6 bg-zinc-300/5 rounded-xl h-full">
        <h3 class="text-base sm:text-lg text-gray-800 dark:text-white text-center flex gap-1 justify-center items-center flex-wrap">
            {{$dataBoxHeader}}
            @if($subtitle)
                <p class="text-gray-600 text-base dark:text-neutral-400 text-center">{{$subtitle}}</p>
            @endif
        </h3>

        <p class="mt-2 text-gray-500 text-accent! dark:text-neutral-200 text-center text-xl sm:text-xl font-bold">
            {{$dataBoxValue}}
        </p>
    </div>
</div>
