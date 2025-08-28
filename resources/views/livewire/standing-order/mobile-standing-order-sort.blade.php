<div>
    <div class="sm:hidden relative" x-data="{open: false}">
        <div>
            <flux:icon.arrow-down-a-z class=" text-accent size-8" x-on:click="open = !open"/>
        </div>
        <div
            class="absolute z-50 left-[-200px] w-[200px] flex flex-col gap-1 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-4"
            x-show="open" x-cloak x-transition x-on:click.outside="open = false">
            <span class="py-1 px-1 rounded-sm hover:bg-accent/90 hover:text-black transition-all cursor-pointer" data-value="desc:customer"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-accent/90 hover:text-black transition-all cursor-pointer" data-value="asc:customer"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-accent/90 hover:text-black transition-all cursor-pointer" data-value="desc:start_from"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Start From (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-accent/90 hover:text-black transition-all cursor-pointer" data-value="asc:start_from"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Start From (abc)</span>
        </div>
    </div>
</div>
