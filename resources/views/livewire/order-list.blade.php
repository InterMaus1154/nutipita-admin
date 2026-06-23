@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@use(App\Enums\OrderStatus)
@use(App\Models\Order)
@use(Illuminate\Support\Str)
@php
    /**
* @var Order $order
 */
@endphp
<div class="space-y-4">
    @if(!$disabled && $orders->isNotEmpty())
        <x-success/>
        <x-error/>
        {{--order summary--}}
        @if(!$disabled && $withSummaryData)
            <livewire:order.order-summary :filters="$filters" :withIncome="$withIncome"
                                          :visibleByDefault="$summaryVisibleByDefault"
                                          :disabled="$disabled"
            />
        @endif
        @if(!$disabled && $orders->isNotEmpty())
            @if(!$browser->isMobile() || $browser->isTablet())
                @include('livewire.order.partials._desktop-table')
            @else
                @include('livewire.order.partials._mobile-cards')
            @endif
        @endif
        <div>
            {{$orders->onEachSide(3)->links(data: ['scrollTo' => false])}}
        </div>
    @else
        <div></div>
    @endif
    <x-loading-indicator />
</div>
