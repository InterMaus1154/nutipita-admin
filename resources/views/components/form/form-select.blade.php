@props(['class' => ''])
<div class="select-wrapper max-w-fit mx-auto">
    <select id="{{$id}}"
            {{$wireModelLive ? "wire:model.live=$wireModelLive" : ""}}
            {{$wireModel ? "wire:model=$wireModel" : ""}}
            name="{{$name}}"
            class="py-3 px-4 pe-9 block w-full max-sm:max-w-[100px] sm:max-w-[130px] bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600 {{$class}}" {{$attributes}}>
        {{$slot}}
    </select>
</div>

