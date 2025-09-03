<div class="flex flex-col gap-4" x-data="{detailsMenuOpen: false}" x-effect="
        if(detailsMenuOpen) {
            document.body.setAttribute('data-menu-open', '');

        } else {
            document.body.removeAttribute('data-menu-open');

        }
     ">
    {{--extra info section--}}
    {{--backdrop--}}
    <div class="fixed inset-0 min-h-screen z-101 bg-black/60" x-show="detailsMenuOpen"
         x-transition x-cloak></div>
    <div
        class="flex-col gap-4 flex fixed bottom-0 z-102 overflow-y-scroll min-h-[60vh] left-0 right-0 dark:bg-zinc-800/80 backdrop-blur-lg rounded-t-xl p-4 border-t-6 border-t-accent"
        x-cloak x-show="detailsMenuOpen" x-on:click.outside="detailsMenuOpen = false"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-full"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-full">
        <div x-on:click="detailsMenuOpen = false" class="absolute right-2 top-2">
            <flux:icon.x-circle class="text-accent size-8"/>
        </div>
        {{$slot}}
    </div>
    <flux:button x-on:click="detailsMenuOpen = !detailsMenuOpen">
        <flux:icon.chevron-double-up class="text-accent"/>
    </flux:button>
</div>
