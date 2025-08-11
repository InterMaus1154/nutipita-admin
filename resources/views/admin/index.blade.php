@use(Carbon\Carbon)
<x-flux-layout>
    <x-page-section>
        @php($week = now()->weekOfYear())
        <x-page-heading title="Today Orders - Week {{$week}}">
            <livewire:homepage-toggler/>
        </x-page-heading>
        <livewire:homepage-orders/>
    </x-page-section>
</x-flux-layout>
