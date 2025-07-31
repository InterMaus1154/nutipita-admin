@props(['products', 'orders', 'withIncome'])
<div class="space-y-4">
    <flux:button id="showSummaryBtn">Show Summaries</flux:button>
    <div @class([
    'hidden' => !$visibleByDefault
]) id="summaryContainer">
        {{--condition to render summary boxes or not--}}
        {{--render only if set to true, and there is a full eloquent collection orders, not paginated version--}}
        @php
            $totalIncome = 0;
            $totalPita = 0;
            $productTotals = [];
            foreach ($orders as $order) {

                // calculate total income for all orders
                $totalIncome += $order->total_price;

                $totalPita += $order->total_pita;

                // calculate product quantity total for each product
                foreach ($products as $product) {
                    if(isset($productTotals[$product->product_name])){
                        $productTotals[$product->product_name] += $order->getTotalOfProduct($product);
                    }else{
                        $productTotals[$product->product_name] = $order->getTotalOfProduct($product);
                    }
                }
            }
        @endphp
        <div class="flex gap-6 flex-wrap">
            @if($withIncome)
                <x-data-box dataBoxHeader="Total Income" :dataBoxValue="moneyFormat($totalIncome)"/>
            @endif
            <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="amountFormat(collect($orders)->count())"/>
            <x-data-box dataBoxHeader="Total Pita" :dataBoxValue="amountFormat($totalPita)"/>

            @foreach($productTotals as $productName => $productQty)
                @unless(empty($productQty))
                    <x-data-box :dataBoxHeader="$productName" :dataBoxValue="amountFormat($productQty)"/>
                @endunless
            @endforeach
        </div>
    </div>
    @push('scripts')
        <script>
            /*
                Control the visibility of summary boxes
             */
            const showSummaryBtn = document.querySelector("#showSummaryBtn");
            const summaryContainer = document.querySelector("#summaryContainer");

            if (summaryContainer.classList.contains('hidden')) {
                showSummaryBtn.innerText = "Show Summaries";
            } else {
                showSummaryBtn.innerText = "Hide Summaries";
            }

            showSummaryBtn.addEventListener("click", () => {
                summaryContainer.classList.toggle("hidden");
                if (summaryContainer.classList.contains('hidden')) {
                    showSummaryBtn.innerText = "Show Summaries";
                } else {
                    showSummaryBtn.innerText = "Hide Summaries";
                }
            });
        </script>
    @endpush
</div>
