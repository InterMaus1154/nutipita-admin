<div>
    <div class="sm:hidden relative" x-data="{open: false}">
        <div>
            <flux:icon.arrow-down-a-z class=" text-accent size-8" x-on:click="open = !open"/>
        </div>
        <div class="absolute z-50 left-[-200px] w-[200px] flex flex-col-reverse gap-1 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-4" x-show="open" x-cloak x-transition x-on:click.outside="open = false">
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:customer" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer Name (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:customer" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer Name (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:order_placed_at" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Placed At (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:order_placed_at" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Placed At (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:order_due_at" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Due At (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:order_due_at" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Due At (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:order_status" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Status (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:order_status" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Status (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:total_pita" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Total Pita (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:total_pita" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Total Pita (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:total_price" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Total Price (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:total_price" wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Total Price (abc)</span>
        </div>
    </div>
</div>
