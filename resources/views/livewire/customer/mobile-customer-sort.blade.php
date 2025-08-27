<div>
    <div class="sm:hidden relative" x-data="{open: false}">
        <div>
            <flux:icon.arrow-down-a-z class=" text-accent size-8" x-on:click="open = !open"/>
        </div>
        <div
            class="absolute z-50 left-[-200px] w-[200px] flex flex-col gap-1 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-4"
            x-show="open" x-cloak x-transition x-on:click.outside="open = false">
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:customer_name"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Name (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:customer_name"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Name (abc)</span>
        </div>
    </div>
</div>
