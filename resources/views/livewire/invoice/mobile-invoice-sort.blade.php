<div>
    <div class="sm:hidden relative" x-data="{open: false}">
        <div>
            <flux:icon.arrow-down-a-z class=" text-accent size-8" x-on:click="open = !open"/>
        </div>
        <div
            class="absolute z-50 left-[-200px] w-[200px] flex flex-col gap-1 border-2 border-neutral-700 rounded-xl bg-zinc-800/60 backdrop-blur-lg p-4"
            x-show="open" x-cloak x-transition x-on:click.outside="open = false">
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:invoice_number"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Invoice Number (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:invoice_number"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Invoice Number (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:customer"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:customer"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Customer (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:invoice_status"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Status (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:invoice_status"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Status (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:invoice_issue_date"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Issue Date (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:invoice_issue_date"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Issue Date (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:invoice_due_date"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Due Date (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:invoice_due_date"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Due Date (abc)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="desc:invoice_total"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Invoice Total (cba)</span>
            <span class="py-1 px-1 rounded-sm hover:bg-neutral-500/50" data-value="asc:invoice_total"
                  wire:click="setMobileSort($event.target.getAttribute('data-value'))" x-on:click="open = false">Invoice Total (abc)</span>
        </div>
    </div>
</div>
