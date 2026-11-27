<?php

namespace Tests\Feature\Infrastructure\Order;

use App\Enums\OrderStatus;
use App\Infrastructure\Order\OrderEloquentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\TestHelpers;

class OrderEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private OrderEloquentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app->make(OrderEloquentRepository::class);
    }

    // ====
    // updateStatusByIds
    // ====

    /**
     * @return array<string, array{OrderStatus}>
     */
    public static function statuses(): array
    {
        $sets = [];
        foreach (OrderStatus::cases() as $status) {
            $sets[$status->name] = [$status];
        }
        return $sets;
    }

    #[DataProvider('statuses')]
    public function test_update_status_by_ids_stores_the_status_name(OrderStatus $status): void
    {
        // start from a different status so the update is a real change
        $from = $status === OrderStatus::G_PAID ? OrderStatus::Y_CONFIRMED : OrderStatus::G_PAID;
        $order = $this->createOrder(status: $from);

        $this->repository->updateStatusByIds([$order->order_id], $status);

        $this->assertSame($status->name, $this->storedOrderStatus($order));
    }

    public function test_update_status_by_ids_updates_every_given_order(): void
    {
        $customer = $this->createCustomer();
        $a = $this->createOrder($customer, OrderStatus::G_PAID);
        $b = $this->createOrder($customer, OrderStatus::O_DELIVERED_UNPAID);
        $c = $this->createOrder($customer, OrderStatus::G_PAID);

        $this->repository->updateStatusByIds([$a->order_id, $b->order_id, $c->order_id], OrderStatus::Y_CONFIRMED);

        foreach ([$a, $b, $c] as $order) {
            $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($order));
        }
    }

    public function test_update_status_by_ids_returns_the_number_of_changed_orders(): void
    {
        $a = $this->createOrder(status: OrderStatus::G_PAID);
        $b = $this->createOrder(status: OrderStatus::G_PAID);

        $count = $this->repository->updateStatusByIds([$a->order_id, $b->order_id], OrderStatus::Y_CONFIRMED);

        $this->assertSame(2, $count);
    }

    public function test_update_status_by_ids_does_not_count_orders_that_already_have_the_status(): void
    {
        $changing = $this->createOrder(status: OrderStatus::G_PAID);
        $already = $this->createOrder(status: OrderStatus::Y_CONFIRMED);

        $count = $this->repository->updateStatusByIds([$changing->order_id, $already->order_id], OrderStatus::Y_CONFIRMED);

        $this->assertSame(1, $count);
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($already));
    }

    public function test_update_status_by_ids_leaves_other_orders_untouched(): void
    {
        $customer = $this->createCustomer();
        $target = $this->createOrder($customer, OrderStatus::G_PAID);
        $sameCustomer = $this->createOrder($customer, OrderStatus::G_PAID);
        $otherCustomer = $this->createOrder(status: OrderStatus::O_DELIVERED_UNPAID);

        $this->repository->updateStatusByIds([$target->order_id], OrderStatus::Y_CONFIRMED);

        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($target));
        $this->assertSame('G_PAID', $this->storedOrderStatus($sameCustomer));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($otherCustomer));
    }

    public function test_update_status_by_ids_with_no_ids_changes_nothing(): void
    {
        $order = $this->createOrder(status: OrderStatus::G_PAID);

        $count = $this->repository->updateStatusByIds([], OrderStatus::Y_CONFIRMED);

        $this->assertSame(0, $count);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_update_status_by_ids_ignores_ids_that_do_not_exist(): void
    {
        $order = $this->createOrder(status: OrderStatus::G_PAID);

        $count = $this->repository->updateStatusByIds([$order->order_id, 999999], OrderStatus::Y_CONFIRMED);

        $this->assertSame(1, $count);
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($order));
    }
}
