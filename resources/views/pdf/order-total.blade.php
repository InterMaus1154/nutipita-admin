<style>
    .product-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .product-box {
        font-size: 1.5rem;
        display: flex;
        gap: 1rem;
    }

</style>

<h1>Order total {{dayDate(now())}}</h1>
<div class="product-list">
    @foreach($productTotals as $key => $value)
        <div class="product-box">
            <span>{{$key}}</span>
            <span>{{$value}}</span>
        </div>
    @endforeach
</div>
<span style="font-weight: bold; font-size: 1.75rem">Total: {{$totalDayPita}}</span>
