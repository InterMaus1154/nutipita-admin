<x-layout>
    <section class="page-section">
        <h2 class="section-title">Today Orders</h2>
        @if($orders->isEmpty())
            <em>No due orders for today!</em>
        @else
            <div class="index-order-cards">
                <div class="index-order-card">
                    <span class="index-order-card-title">Daily Income</span>
                    <span class="index-order-card-value">£{{$totalDayIncome}}</span>
                </div>
                @foreach($products as $product)
                    <div class="index-order-card">
                        <div class="index-order-card-title">Total of {{$product->product_name}}</div>
                        <div class="index-order-card-value">{{$productTotals[$product->product_id]}}</div>
                    </div>
                @endforeach
            </div>
            @include('livewire.order-list')
        @endif
    </section>
</x-layout>
