<?php

namespace Tests\Feature\Admin;

use App\Models\RepairOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepairOrderSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_can_be_searched_by_client_car_and_master_data(): void
    {
        $user = User::factory()->create();

        $orders = [
            'Олександр' => RepairOrder::query()->create([
                'client_name' => 'Олександр Коваль',
                'client_phone' => '+380501111111',
                'car_info' => 'VW Touareg',
                'master_name' => 'Іван',
            ]),
            '502222222' => RepairOrder::query()->create([
                'client_name' => 'Марія Бондар',
                'client_phone' => '+380502222222',
                'car_info' => 'BMW X5',
                'master_name' => 'Петро',
            ]),
            'Audi Q7' => RepairOrder::query()->create([
                'client_name' => 'Дмитро Мельник',
                'client_phone' => '+380503333333',
                'car_info' => 'Audi Q7 2018',
                'master_name' => 'Микола',
            ]),
            'Сергій' => RepairOrder::query()->create([
                'client_name' => 'Наталія Шевченко',
                'client_phone' => '+380504444444',
                'car_info' => 'Mercedes GLE',
                'master_name' => 'Сергій',
            ]),
        ];

        foreach ($orders as $search => $expectedOrder) {
            $this->actingAs($user)
                ->getJson(route('admin.repair-orders.list', ['search' => $search]))
                ->assertOk()
                ->assertJsonCount(1, 'items')
                ->assertJsonPath('items.0.id', $expectedOrder->id);
        }
    }
}
