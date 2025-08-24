<div
    {{$attributes->merge([
    'class' => 'relative'
])}}
>
    <flux:icon.adjustments-horizontal x-on:click="actionMenuOpen = !actionMenuOpen"/>
    {{--box--}}
    <div x-show="actionMenuOpen"
         x-on:click.outside="actionMenuOpen = false"
         x-transition
         x-cloak
         class="absolute z-100 left-[-9rem] top-5 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-4 flex flex-col gap-4 min-w-[200px] action">
            {{$slot}}
    </div>
</div>
