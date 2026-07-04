<div class="flex flex-col">
    <x-error/>
    <x-success/>
    @if($isMobile)
        @include('livewire.customer.partials._mobile-cards')
    @else
        @include('livewire.customer.partials._desktop-table')
    @endif
</div>
