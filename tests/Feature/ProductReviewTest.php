<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;
    private User $seller;
    private Company $company;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create(['role' => 'business', 'email_verified_at' => now()]);
        $this->company = Company::create([
            'user_id' => $this->seller->id,
            'name' => 'Artesanías del Valle',
            'nit' => '900123456-7',
            'status' => 'active',
            'is_formal' => true,
        ]);

        $this->product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Bolso Wayuu Tradicional',
            'description' => 'Bolso artesanal tejido a mano',
            'price' => 120000,
            'stock' => 10,
            'active' => true,
        ]);

        $this->buyer = User::factory()->create(['role' => 'user', 'name' => 'Carlos Cliente', 'email_verified_at' => now()]);
    }

    public function test_guest_cannot_submit_review(): void
    {
        $response = $this->post(route('public.product.review', $this->product), [
            'rating' => 5,
            'comment' => 'Excelente calidad de tejido.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_authenticated_user_can_submit_review(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('public.product.review', $this->product), [
            'rating' => 5,
            'comment' => 'Me encantó este bolso, muy buena calidad y textura.',
        ]);

        $response->assertRedirect(route('public.product', $this->product));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->buyer->id,
            'rating' => 5,
            'comment' => 'Me encantó este bolso, muy buena calidad y textura.',
            'is_verified_purchase' => false,
        ]);

        $this->assertEquals(1, $this->product->fresh()->reviews_count);
        $this->assertEquals(5.0, $this->product->fresh()->average_rating);
    }

    public function test_purchased_user_review_is_marked_verified(): void
    {
        // Crear orden de compra para el usuario
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'company_id' => $this->company->id,
            'total' => 120000,
            'status' => 'paid',
            'payment_method' => 'nequi',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'company_id' => $this->company->id,
            'quantity' => 1,
            'unit_price' => 120000,
            'subtotal' => 120000,
            'product_name' => $this->product->name,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('public.product.review', $this->product), [
            'rating' => 4,
            'comment' => 'Excelente producto original.',
        ]);

        $response->assertRedirect(route('public.product', $this->product));

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->buyer->id,
            'rating' => 4,
            'is_verified_purchase' => true,
        ]);
    }

    public function test_user_can_update_existing_review_without_duplicate(): void
    {
        Review::create([
            'product_id' => $this->product->id,
            'user_id' => $this->buyer->id,
            'rating' => 3,
            'comment' => 'Regular al inicio.',
            'is_verified_purchase' => false,
        ]);

        $response = $this->actingAs($this->buyer)->post(route('public.product.review', $this->product), [
            'rating' => 5,
            'comment' => 'Cambié de opinión, el soporte me ayudó y es excelente.',
        ]);

        $response->assertRedirect(route('public.product', $this->product));
        $this->assertDatabaseCount('reviews', 1);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->buyer->id,
            'rating' => 5,
            'comment' => 'Cambié de opinión, el soporte me ayudó y es excelente.',
        ]);
    }

    public function test_validation_rules_for_review(): void
    {
        $response = $this->actingAs($this->buyer)->post(route('public.product.review', $this->product), [
            'rating' => 6, // Fuera de rango
            'comment' => 'ab', // Demasiado corto
        ]);

        $response->assertSessionHasErrors(['rating', 'comment']);
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_product_view_displays_reviews_and_average(): void
    {
        Review::create([
            'product_id' => $this->product->id,
            'user_id' => $this->buyer->id,
            'rating' => 5,
            'comment' => 'Comentario visible en la vista pública.',
            'is_verified_purchase' => true,
        ]);

        $response = $this->get(route('public.product', $this->product));

        $response->assertStatus(200);
        $response->assertSee('Bolso Wayuu Tradicional');
        $response->assertSee('Comentario visible en la vista pública.');
        $response->assertSee('Carlos Cliente');
        $response->assertSee('5.0');
    }
}
