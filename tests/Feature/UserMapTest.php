<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_map_with_enriched_companies()
    {
        $user = User::factory()->create(['role' => 'user']);
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Comercio Test',
            'category' => 'Tecnología',
            'latitude' => 5.3086,
            'longitude' => -73.8149,
            'status' => 'active',
            'opening_time' => '08:00',
            'closing_time' => '18:00',
        ]);
        Product::create([
            'company_id' => $company->id,
            'name' => 'Producto Test',
            'price' => 25000,
            'stock' => 10,
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('user.map'));

        $response->assertStatus(200);
        $response->assertViewIs('user.map');
        $response->assertViewHas('companies');
        $response->assertViewHas('categories');

        $companies = $response->viewData('companies');
        $this->assertNotEmpty($companies);

        $firstCompany = $companies->first();
        $this->assertArrayHasKey('name', $firstCompany);
        $this->assertArrayHasKey('logo_url', $firstCompany);
        $this->assertArrayHasKey('is_open', $firstCompany);
        $this->assertArrayHasKey('schedule_label', $firstCompany);
        $this->assertArrayHasKey('rating', $firstCompany);
        $this->assertArrayHasKey('reviews_count', $firstCompany);
        $this->assertArrayHasKey('products', $firstCompany);
    }

    public function test_ajax_add_to_cart_returns_json_response()
    {
        $user = User::factory()->create(['role' => 'user']);
        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Comercio Test 2',
            'category' => 'Moda',
            'latitude' => 5.3086,
            'longitude' => -73.8149,
            'status' => 'active',
        ]);
        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Producto Test 2',
            'price' => 35000,
            'stock' => 5,
            'active' => true,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('user.cart.add', $product->id), [
                'quantity' => 1
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'message', 'cart_count', 'product_id']);
    }
}
