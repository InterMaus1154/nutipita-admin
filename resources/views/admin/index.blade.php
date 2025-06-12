<x-flux-layout>
    <section class="page-section">
        <h2 class="section-title">Today Orders</h2>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <a href="{{route('today.order.pdf')}}" class="action-link">Download total order PDF</a>
            <div class="index-order-cards">
                <div class="index-order-card">
                    <span class="index-order-card-title">Daily Income</span>
                    <span class="index-order-card-value">£{{$totalDayIncome}}</span>
                </div>
                <div class="index-order-card">
                    <span class="index-order-card-title">Total pita</span>
                    <span class="index-order-card-value">{{$totalDayPita}}</span>
                </div>
                @foreach($productTotals as $key => $value)
                    <div class="index-order-card">
                        <div class="index-order-card-title">{{$key}}</div>
                        <div class="index-order-card-value">{{$value}}</div>
                    </div>
                @endforeach
            </div>
            @include('livewire.order-list')
        @endif
    </section>
</x-flux-layout>
