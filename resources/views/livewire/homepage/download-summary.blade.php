{{--download order summaries for the selected day--}}
<div class="flex relative" x-data="{menuOpen: false}">
    <flux:icon.folder-arrow-down class="size-8 text-accent cursor-pointer" x-on:click="menuOpen = !menuOpen"/>
    <div class="absolute -left-60 top-8 z-50 border-2 border-neutral-700 rounded-xl bg-zinc-800 p-2 flex flex-col gap-6 min-w-[350px]" x-show="menuOpen" x-on:click.outside="menuOpen = false" x-cloak>
        <div class="py-1 px-4 rounded-sm flex gap-1 hover:bg-neutral-500/50 cursor-pointer" wire:click="createSummary('day')">
            <flux:icon.sun class="text-accent"/>
            Day Summary
        </div>
        <div class="py-1 px-4 rounded-sm flex gap-1 hover:bg-neutral-500/50 cursor-pointer" wire:click="createSummary('night')">
            <flux:icon.moon class="text-accent"/>
            Night Summary
        </div>
        <div class="py-1 px-4 rounded-sm flex gap-1 hover:bg-neutral-500/50 cursor-pointer" wire:click="createSummary('both')">
            <flux:icon.sun class="text-accent"/>
            <flux:icon.moon class="text-accent"/>
            Day/Night Summary
        </div>
    </div>
</div>
