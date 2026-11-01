@php use App\Models\Order; @endphp
@php
    /** @var \App\Domain\Invoice\Invoice $invoice */
@endphp
<html>
<head>
    <title>Invoice {{$invoice->getInvoiceNumber()}} Summary</title>
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
    <h1 style="text-align: center">Invoice {{$invoice->getInvoiceNumber()}} Summary</h1>
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
    <tr>
        <th>#</th>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Product Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $index => $invoiceProduct)
        <tr>
            <td style="text-align: center">{{$index+1}}</td>
            <td style="text-align: center">{{$invoiceProduct->product->getName()}} {{$invoiceProduct->product->getWeight()}}
                g
            </td>
            <td style="text-align: center">{{$invoiceProduct->product_qty}}</td>
            <td style="text-align: center">{{moneyFormat($invoiceProduct->product_unit_price)}}</td>
            <td style="text-align: center">{{moneyFormat($invoiceProduct->product_qty * $invoiceProduct->product_unit_price)}}</td>
        </tr>
    @endforeach
    @if(! is_null($invoice->getDeliveryCharge()))
        <tr>
            <td>#</td>
            <td style="text-align: center">Delivery Charge</td>
            <td></td>
            <td></td>
            <td style="text-align: center">{{moneyFormat($invoice->getDeliveryCharge())}}</td>
        </tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td>#</td>
        <td></td>
        <td></td>
        <td style="text-align: center; font-weight: bold">
            <br>
        </td>
        <td></td>
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
    @if($customer->getId() === 101)
        {{--only for Arik--}}
        <tr>
            <td style="text-align: left; font-weight: bold; border: 0; padding: 0; font-size: 1.5rem;">
            </td>
            <td style="text-align: right; font-weight: normal; border: 0; padding: 0; font-size: 1.125rem">

            </td>
        </tr>
    @endif
</table>
</body>
</html>
