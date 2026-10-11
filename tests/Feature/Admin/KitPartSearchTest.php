<?php

namespace Tests\Feature\Admin;

use App\Models\Kit;
use App\Models\KitPart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitPartSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_can_be_searched_by_title_or_barcode(): void
    {
        $user = User::factory()->create();
        $kit = Kit::query()->create([
            'title' => 'Основний комплект',
            'purchase_price' => 0,
            'sale_price' => 0,
            'status' => 'active',
        ]);

        $partByTitle = KitPart::query()->create([
            'kit_id' => $kit->id,
            'sequence_number' => 1,
            'title' => 'Захисна колба',
            'barcode' => 'PART-K0001-P0001',
            'status' => 'in_stock',
        ]);
        $partByBarcode = KitPart::query()->create([
            'kit_id' => $kit->id,
            'sequence_number' => 2,
            'title' => 'Відбійник',
            'barcode' => 'SPECIAL-002',
            'status' => 'in_stock',
        ]);
        KitPart::query()->create([
            'kit_id' => $kit->id,
            'sequence_number' => 3,
            'title' => 'Подушка',
            'barcode' => 'PART-K0001-P0003',
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)
            ->getJson(route('admin.kit-parts.list', ['search' => 'колба']))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $partByTitle->id);

        $this->actingAs($user)
            ->getJson(route('admin.kit-parts.list', ['search' => 'SPECIAL']))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $partByBarcode->id);
    }

    public function test_search_is_combined_with_the_existing_kit_filter(): void
    {
        $user = User::factory()->create();
        $firstKit = Kit::query()->create([
            'title' => 'Перший комплект',
            'purchase_price' => 0,
            'sale_price' => 0,
            'status' => 'active',
        ]);
        $secondKit = Kit::query()->create([
            'title' => 'Другий комплект',
            'purchase_price' => 0,
            'sale_price' => 0,
            'status' => 'active',
        ]);

        $expectedPart = KitPart::query()->create([
            'kit_id' => $firstKit->id,
            'sequence_number' => 1,
            'title' => 'Колба',
            'barcode' => 'FIRST-001',
            'status' => 'in_stock',
        ]);
        KitPart::query()->create([
            'kit_id' => $secondKit->id,
            'sequence_number' => 1,
            'title' => 'Колба',
            'barcode' => 'SECOND-001',
            'status' => 'in_stock',
        ]);

        $this->actingAs($user)
            ->getJson(route('admin.kit-parts.list', [
                'kit_id' => $firstKit->id,
                'search' => 'Колба',
            ]))
            ->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $expectedPart->id);
    }
}
