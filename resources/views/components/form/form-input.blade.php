@props(['class' => ''])
<input type="{{$type ?? "text"}}"
       id="{{$id}}"
       {{$wireModelLive ? "wire:model.live=$wireModelLive" : ""}}
       {{$wireModel ? "wire:model=$wireModel" : ""}}
       placeholder="{{$placeholder}}"
       {{isset($wireKey) ? "wire:key=$wireKey" : ""}}
       name="{{$name}}"
       {{$attributes}}
       class="py-2.5 sm:py-3 px-4 block border-gray-400 rounded-lg sm:text-sm focus:border-accent disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-accent w-full {{$class}}"
>
