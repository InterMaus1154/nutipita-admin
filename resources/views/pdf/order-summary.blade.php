<html>
<head>
    <title>Order Summary</title>
    <style>
        *{
            margin: 0;
        }

        body{
            padding: 1rem;
        }

        header {
            margin: 2rem 0;
        }

        header *{
            margin: 6px 0;
        }

        table{
            border-collapse: collapse;
            margin: 0.5rem 0;
        }
        table th, td{
            padding: 1rem 0.5rem;
            border: 1px solid black;
        }
    </style>
</head>
<body>
<header>
    <h1 style="text-align: center">Order Summary</h1>
    <p style="text-align: center">(This is not an invoice)</p>
    <table style="width: 100%; border: none">
        <tr>
            <td style="text-align: left; font-size: 1.5rem; font-weight: bold; border: none; padding: 0">
                Total orders: {{$orders->count()}}
            </td>
            <td style="text-align: right; font-size: 1.5rem; font-weight: bold; border: none; padding: 0">
                For {{$customer->customer_name}}
            </td>
        </tr>
    </table>
</header>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Order Placed At</th>
        <th>Order Due At</th>
        @foreach($products as $product)
            <th>{{$product->product_name}} {{$product->product_weight_g}}g
            </th>
        @endforeach
        <th>Order Total</th>
    </tr>
    </thead>
    <tbody>
        @foreach($orders as $index => $order)

            <tr>
                <td>{{$index+1}}</td>
                <td>@dayDate($order->order_placed_at)</td>
                <td>@dayDate($order->order_due_at)</td>
                @foreach($products as $product)
                    @php($orderProduct = $order->products->firstWhere('product_id', $product->product_id))
                    @if(is_null($orderProduct))
                        <td style="text-align: center">-</td>
                    @else
                        <td style="text-align: center">
                            @amountFormat($orderProduct->pivot->product_qty) x @unitPriceFormat($orderProduct->pivot->order_product_unit_price) <br>
                            @moneyFormat($orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price)
                        </td>
                    @endif
                @endforeach
                <td style="text-align: center">
                    @moneyFormat($order->total_price)
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<h2 style="text-align: right">
    Total for period: @moneyFormat($periodTotal)
</h2>
</body>
</html>
