<div class="flex flex-col gap-4">
    <div
        class="flex-col gap-4 flex fixed bottom-0 z-102 overflow-y-scroll min-h-[60vh] left-0 right-0 dark:bg-zinc-800/80 backdrop-blur-lg rounded-t-xl p-4 border-t-6 border-t-accent"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full">
        <div x-on:click="$dispatch('modal-close')" class="absolute right-2 top-2">
            <flux:icon.x-circle class="text-accent size-8"/>
        </div>
        {{$slot}}
    </div>
</div>
