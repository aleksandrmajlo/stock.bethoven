<?php

namespace Tests\Feature\Admin;

use App\Models\Kit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_can_be_searched_by_title(): void
    {
        $user = User::factory()->create();
        $expectedKit = Kit::query()->create([
            'title' => 'Пневмоподушка VW Touareg',
            'purchase_price' => 100,
            'sale_price' => 150,
            'status' => 'in_stock',
        ]);
        Kit::query()->create([
            'title' => 'Компресор BMW X5',
            'purchase_price' => 200,
            'sale_price' => 300,
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)
            ->getJson(route('admin.kits.list', ['search' => 'Touareg']))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $expectedKit->id)
            ->assertJsonPath('items.0.title', $expectedKit->title);
    }
}
