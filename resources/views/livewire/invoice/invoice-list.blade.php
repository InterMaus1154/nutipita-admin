@use(App\Enums\InvoiceStatus)
<div class="space-y-4">
    <x-success/>
    <x-error/>
    @include('livewire.invoice.partials._summary-cards')
    @if($isMobile)
        @include('livewire.invoice.partials._mobile-cards')
    @else
        @include('livewire.invoice.partials._desktop-table')
    @endif
    @unless($isMobile)
        <div>
            {{$invoices->onEachSide(3)->links(data: ['scrollTo' => false])}}
        </div>
    @endunless
    <x-loading-indicator/>
</div>
