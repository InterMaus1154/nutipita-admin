@php use App\DataTransferObjects\OrderSummaryDto; @endphp
@php
    /**
* @var OrderSummaryDto $summaryDto
 */
@endphp
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

<h1>{{$type === "both" ? "Day and Night" : ucfirst($type)}} Order Total {{dayDate(now())}}</h1>
<div class="product-list">
    @foreach($summaryDto->productTotalDTOs() as $productTotal)
        <div class="product-box">
            <span>{{$productTotal->name()}}</span>
            <span>{{$productTotal->total()}}</span>
        </div>
    @endforeach
</div>
<span style="font-weight: bold; font-size: 1.75rem">Total: {{$summaryDto->productTotals()}}</span>
