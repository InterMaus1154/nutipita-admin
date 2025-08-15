@use(Carbon\Carbon)
<x-flux-layout>
    <x-page-section>
        @php($week = now()->weekOfYear())
        <x-page-heading title="Today Orders - Week {{$week}}">
            <div class="flex gap-4 items-center">
                <livewire:homepage.download-summary />
                <livewire:homepage.homepage-toggler/>
            </div>
        </x-page-heading>
        <livewire:homepage.homepage-orders/>
    </x-page-section>
</x-flux-layout>
