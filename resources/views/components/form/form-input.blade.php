<input type="{{$type ?? "text"}}"
       id="{{$id}}"
       {{$wireModelLive ? "wire:model.live=$wireModelLive" : ""}}
       {{$wireModel ? "wire:model=$wireModel" : ""}}
       placeholder="{{$placeholder}}"
       name="{{$name}}"
       {{$attributes}}
       class="py-2.5 sm:py-3 px-4 block border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600 {{$noFullWidth ? "" : "w-full"}}"
>
