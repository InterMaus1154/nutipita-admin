@use(App\Domain\Pdf\Enums\InvoiceSummaryDisplayMode)
@php
    /** @var \App\Domain\Pdf\Entities\InvoiceSummaryData $data */
    $invoice = $data->invoice;
    $renderOrders = $data->displayMode === InvoiceSummaryDisplayMode::RENDER_ORDERS;
    $renderProducts = $data->displayMode === InvoiceSummaryDisplayMode::RENDER_PRODUCTS;
    $customer = $invoice->getCustomer();

    // orders mode: one column per product that appears in any of the orders, in name order
    $productColumns = collect();
    if ($renderOrders) {
        $productColumns = collect($data->orderData)
            ->flatMap(fn($order) => $order->products)
            ->map(fn($line) => $line->product)
            ->unique('product_id')
            ->sortBy(fn($product) => [strtolower($product->getName()), $product->getWeight() ?? 0])
            ->values();
    }

    // the label cells of the footer span every column except the last (the amount)
    $footerSpan = $renderOrders ? $productColumns->count() + 1 : 4;
@endphp
<html>
<head>
    <title>Invoice #{{$invoice->getInvoiceNumber()}} Summary</title>
    <style>
        * {
            margin: 0;
        }

        body {
            padding: 1rem;
        }

        header {
            margin: 1rem 0;
        }

        header * {
            margin: 6px 0;
        }

        table {
            border-collapse: collapse;
        }

        table th, td {
            padding: 1rem 0.5rem;
            border: 1px solid black;
        }
    </style>
</head>
<body>
<header>
    <h1 style="text-align: center">Invoice #{{$invoice->getInvoiceNumber()}} Summary</h1>
    <p style="text-align: center">(This is not the invoice)</p>
    <table style="width: 100%; border: none">
        <tr>
            <td style="text-align: left; font-size: 1.5rem; font-weight: bold; border: none; padding: 0">
                {{$customer->getName()}}
            </td>
            <td style="text-align: right; font-size: 1.25rem; font-weight: bold; border: none; padding: 0">
                @if(! is_null($invoice->getInvoiceFrom()) && ! is_null($invoice->getInvoiceTo()))
                    {{$invoice->getInvoiceFrom()->format('m/d/Y')}} - {{$invoice->getInvoiceTo()->format('m/d/Y')}}
                @endif
            </td>
        </tr>
    </table>
</header>
<table style="width: 100%; margin-bottom: 1rem">
    <thead>
    @if($renderOrders)
        <tr>
            <th>Order Due Date</th>
            @foreach($productColumns as $column)
                <th>{{$column->getName()}}@if($column->hasWeight()) {{$column->getWeight()}}g @endif</th>
            @endforeach
            <th>Order Total</th>
        </tr>
    @else
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Item Total</th>
        </tr>
    @endif
    </thead>
    <tbody>
    @if($renderOrders)
        @foreach($data->orderData as $order)
            @php
                $lines = collect($order->products)->keyBy(fn($line) => $line->product->product_id);
            @endphp
            <tr>
                <td style="text-align: center">{{dayDate($order->due)}}</td>
                @foreach($productColumns as $column)
                    @php
                        $line = $lines->get($column->product_id);
                    @endphp
                    @if(is_null($line))
                        <td style="text-align: center">-</td>
                    @else
                        <td style="text-align: center">
                            {{amountFormat($line->qty)}} x {{unitPriceFormat($line->unitPrice)}}
                            <br>
                            {{moneyFormat($line->total)}}
                        </td>
                    @endif
                @endforeach
                <td style="text-align: center">{{moneyFormat(collect($order->products)->sum('total'))}}</td>
            </tr>
        @endforeach
        <tr style="font-weight: bold">
            <td style="text-align: center">Total</td>
            @foreach($productColumns as $column)
                @php
                    $columnLines = collect($data->orderData)
                        ->flatMap(fn($order) => $order->products)
                        ->filter(fn($line) => $line->product->product_id === $column->product_id);
                    $prices = $columnLines->pluck('unitPrice')->unique();
                @endphp
                <td style="text-align: center">
                    {{amountFormat($columnLines->sum('qty'))}}@if($prices->count() === 1) x {{unitPriceFormat($prices->first())}}@endif
                    <br>
                    {{moneyFormat($columnLines->sum('total'))}}
                </td>
            @endforeach
            <td style="text-align: center">{{moneyFormat(collect($data->orderData)->flatMap(fn($order) => $order->products)->sum('total'))}}</td>
        </tr>
        @if(! $invoice->getDeliveryCharge()->isNull())
            <tr>
                <td colspan="{{$footerSpan}}" style="text-align: right">Nuti Pita / Delivery Charge ({{$invoice->getDeliveryCharge()->getModifier()}} x {{moneyFormat($invoice->getDeliveryCharge()->toFloat())}})</td>
                <td style="text-align: center">{{moneyFormat($invoice->getDeliveryCharge()->getCalculated())}}</td>
            </tr>
        @endif
    @else
        @foreach($data->productData as $index => $line)
            <tr>
                <td style="text-align: center">{{$index+1}}</td>
                <td style="text-align: center">{{$line->product->getName()}}@if($line->product->hasWeight()) {{$line->product->getWeight()}}g @endif</td>
                <td style="text-align: center">{{numberFormat($line->qty, 0)}}</td>
                <td style="text-align: center">{{moneyFormat($line->unitPrice)}}</td>
                <td style="text-align: center">{{moneyFormat($line->total)}}</td>
            </tr>
        @endforeach
        @if(! $invoice->getDeliveryCharge()->isNull())
            <tr>
                <td>#</td>
                <td style="text-align: center">Nuti Pita / Delivery Charge</td>
                <td style="text-align: center;">{{$invoice->getDeliveryCharge()->getModifier()}}</td>
                <td style="text-align: center;">{{moneyFormat($invoice->getDeliveryCharge()->toFloat())}}</td>
                <td style="text-align: center">{{moneyFormat($invoice->getDeliveryCharge()->getCalculated())}}</td>
            </tr>
        @endif
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td style="font-weight: bold" colspan="{{$footerSpan}}">Sub Total</td>
        <td style="text-align: center;"> {{moneyFormat($invoice->getTotal()->subtotal())}} </td>
    </tr>
    @if($invoice->getTotal()->hasCredit())
        <tr>
            <td style="font-weight: bold" colspan="{{$footerSpan}}">Credit Applied</td>
            <td style="text-align: center;"> {{moneyFormat($invoice->getTotal()->credit())}} </td>
        </tr>
    @endif
    <tr>
        <td style="font-weight: bold" colspan="{{$footerSpan}}">Total</td>
        <td style="text-align: center;"> {{moneyFormat($invoice->getTotal()->total())}} </td>
    </tr>
    </tfoot>
</table>
<table style="width: 100%">
    <tr>
        <td style="text-align: left; font-weight: bold; border: 0; padding: 0; font-size: 1.5rem;">

        </td>
        <td style="text-align: right; font-weight: bold; border: 0; padding: 0; font-size: 1.5rem">

        </td>
    </tr>
</table>
</body>
</html>
