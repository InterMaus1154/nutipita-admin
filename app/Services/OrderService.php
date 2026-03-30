<?php

namespace App\Services;

use App\DataTransferObjects\OrderSummaryDto;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;

class OrderService
{
    /**
     * Create a basic summary without customer from orders
     * Includes product totals and total (no income)
     * @param array $filters
     * @return OrderSummaryDto
     */
    public function createBasicSummary(array $filters): OrderSummaryDto
    {
        $orders = Order::query()
            ->when($filters['nighttime_only'], function ($builder) {
                return $builder->where('is_daytime', false);
            })
            ->when($filters['daytime_only'], function ($builder) {
                return $builder->where('orders.is_daytime', true);
            })
            ->when(!empty($filters['due_from']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
            ->when(!empty($filters['due_to']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
            })
            ->when(!empty($filters['both']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '=', today()->toDateString())
                    ->where('is_daytime', true)
                    ->orWhereDate('order_due_at', '=', today()->addDay()->toDateString());
            })
            ->with('customer:customer_id,customer_name', 'products')
            ->get();

        return OrderSummaryDto::from($orders);
    }

    public function createOrder(int|string|Customer $customer_id,
                                string|Carbon       $order_due_at,
                                string|Carbon       $order_placed_at,
                                array|Collection    $products,
                                string              $shift = 'night'): Order
    {
        $customer = resolveModel($customer_id, Customer::class);

        if (is_array($products)) {
            $products = collect($products);
        }

        $products = $products->filter(fn(int $qty) => $qty > 0);

        if ($products->isEmpty()) throw new InvalidArgumentException('All products cannot be empty!');

        DB::beginTransaction();
        try {

            $order = $customer->orders()->create([
                'customer_id' => $customer_id,
                'order_placed_at' => $order_placed_at,
                'order_due_at' => $order_due_at,
                'is_daytime' => $shift === 'day',
                'order_status' => OrderStatus::Y_CONFIRMED->name
            ]);

            foreach ($products as $id => $qty) {
                $product = Product::find($id)->setCurrentCustomer($customer_id);

                $order->products()->attach($id, [
                    'product_qty' => $qty,
                    'order_product_unit_price' => $product->price
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error at creating order');
            Log::error($e->getMessage());
            throw new Exception('Error at creating order');
        }

        return $order;
    }
}
