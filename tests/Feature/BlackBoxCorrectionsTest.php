<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BlackBoxCorrectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_requires_password_with_uppercase_number_symbol_and_8_chars(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        // 1. Menos de 8 caracteres
        $response = $this->post('/register', [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos.test@correo.com',
            'password'              => 'Ab1!',
            'password_confirmation' => 'Ab1!',
            'role'                  => 'user',
        ]);
        $response->assertSessionHasErrors(['password']);

        // 2. Sin mayúscula
        $response2 = $this->post('/register', [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos.test@correo.com',
            'password'              => 'password123!',
            'password_confirmation' => 'password123!',
            'role'                  => 'user',
        ]);
        $response2->assertSessionHasErrors(['password']);

        // 3. Sin número
        $response3 = $this->post('/register', [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos.test@correo.com',
            'password'              => 'Password!abc',
            'password_confirmation' => 'Password!abc',
            'role'                  => 'user',
        ]);
        $response3->assertSessionHasErrors(['password']);

        // 4. Sin signo especial
        $response4 = $this->post('/register', [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos.test@correo.com',
            'password'              => 'Password1234',
            'password_confirmation' => 'Password1234',
            'role'                  => 'user',
        ]);
        $response4->assertSessionHasErrors(['password']);

        // 5. Contraseña válida y registro SIN celular (Punto 8)
        $response5 = $this->post('/register', [
            'name'                  => 'Carlos Ruiz',
            'email'                 => 'carlos.valido@correo.com',
            'password'              => 'Contrasena123!',
            'password_confirmation' => 'Contrasena123!',
            'role'                  => 'user',
        ]);
        $response5->assertSessionDoesntHaveErrors(['password', 'email', 'phone']);
        $this->assertDatabaseHas('users', ['email' => 'carlos.valido@correo.com']);
    }

    public function test_registration_rejects_duplicate_or_invalid_email(): void
    {
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        User::factory()->create(['email' => 'existente@spotlight.com']);

        // Correo duplicado
        $response1 = $this->post('/register', [
            'name'                  => 'Usuario Dos',
            'email'                 => 'existente@spotlight.com',
            'password'              => 'ClaveSegura123#',
            'password_confirmation' => 'ClaveSegura123#',
            'role'                  => 'user',
        ]);
        $response1->assertSessionHasErrors(['email']);

        // Formato inválido
        $response2 = $this->post('/register', [
            'name'                  => 'Usuario Dos',
            'email'                 => 'formato-invalido-sin-arroba',
            'password'              => 'ClaveSegura123#',
            'password_confirmation' => 'ClaveSegura123#',
            'role'                  => 'user',
        ]);
        $response2->assertSessionHasErrors(['email']);
    }

    public function test_cart_blocks_adding_when_stock_is_insufficient_and_returns_error_message(): void
    {
        $seller = User::factory()->create(['role' => 'business']);
        $company = Company::create([
            'user_id' => $seller->id,
            'name'    => 'Tienda Stock',
            'status'  => 'active',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name'       => 'Mochila Artesanal',
            'price'      => 80000,
            'stock'      => 3,
            'active'     => true,
        ]);

        $buyer = User::factory()->create(['role' => 'user']);
        $this->actingAs($buyer);

        // 1. Agregar más de las 3 disponibles en una sola petición
        $response1 = $this->postJson(route('user.cart.add', $product), ['quantity' => 5]);
        $response1->assertStatus(422);
        $response1->assertJsonFragment(['insufficient_stock' => true]);

        // 2. Agregar 2 válidas
        $response2 = $this->postJson(route('user.cart.add', $product), ['quantity' => 2]);
        $response2->assertStatus(200);

        // 3. Intentar agregar 2 más (total 4, supera stock de 3)
        $response3 = $this->postJson(route('user.cart.add', $product), ['quantity' => 2]);
        $response3->assertStatus(422);
        $response3->assertJsonFragment(['insufficient_stock' => true]);
    }

    public function test_admin_cannot_edit_users_but_can_verify_accounts_and_delete(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create([
            'role'              => 'user',
            'email_verified_at' => null,
            'name'              => 'Comprador Pendiente',
        ]);

        // Admin intenta acceder a edit -> debe redirigir con advertencia de restricción
        $editResponse = $this->actingAs($admin)->get(route('admin.users.edit', $targetUser));
        $editResponse->assertRedirect(route('admin.users.index'));
        $editResponse->assertSessionHas('warning');

        // Admin verifica la cuenta directamente
        $verifyResponse = $this->actingAs($admin)->patch(route('admin.users.verify', $targetUser));
        $verifyResponse->assertSessionHas('success');
        $this->assertNotNull($targetUser->fresh()->email_verified_at);

        // Admin elimina la cuenta
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.users.destroy', $targetUser));
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_user_profile_panel_renders_purchases_and_shipment_tracking_guide(): void
    {
        $buyer = User::factory()->create(['role' => 'user', 'name' => 'Valeria Gómez']);
        $seller = User::factory()->create(['role' => 'business']);
        $company = Company::create([
            'user_id' => $seller->id,
            'name'    => 'Artesanías Andinas Ubaté',
            'status'  => 'active',
        ]);
        $product = Product::create([
            'company_id' => $company->id,
            'name'       => 'Ruana Tejida de Lana',
            'price'      => 120000,
            'stock'      => 10,
            'active'     => true,
        ]);

        $order = Order::create([
            'user_id'             => $buyer->id,
            'company_id'          => $company->id,
            'total'               => 120000,
            'status'              => 'shipped',
            'payment_status'      => 'approved',
            'payment_method_code' => '48',
        ]);

        OrderDetail::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'company_id'   => $company->id,
            'product_name' => $product->name,
            'quantity'     => 1,
            'unit_price'   => 120000,
            'subtotal'     => 120000,
        ]);

        $shipment = Shipment::create([
            'order_id'            => $order->id,
            'company_id'          => $company->id,
            'tracking_number'     => 'CRD-PANEL-TEST-01',
            'carrier'             => 'coordinadora',
            'carrier_name'        => 'Coordinadora Mercantil',
            'status'              => 'in_transit',
            'origin_city'         => 'Ubaté, Cundinamarca',
            'destination_city'    => 'Bogotá D.C.',
            'destination_address' => 'Carrera 15 # 93-40',
            'recipient_name'      => 'Valeria Gómez',
        ]);

        $response = $this->actingAs($buyer)->get(route('user.panel'));
        $response->assertStatus(200);
        $response->assertSee('CRD-PANEL-TEST-01');
        $response->assertSee('Coordinadora Mercantil');
        $response->assertSee('Artesanías Andinas Ubaté');
        $response->assertSee(route('user.orders.tracking', $order));
    }

    public function test_phone_number_must_have_exact_10_digits(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // 9 dígitos (menos de 10)
        $respShort = $this->actingAs($user)->put(route('user.profile.update'), [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => '310123456', // 9 dígitos
        ]);
        $respShort->assertSessionHasErrors(['phone']);

        // 11 dígitos (más de 10)
        $respLong = $this->actingAs($user)->put(route('user.profile.update'), [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => '31012345678', // 11 dígitos
        ]);
        $respLong->assertSessionHasErrors(['phone']);

        // 10 dígitos exactos
        $respExact = $this->actingAs($user)->put(route('user.profile.update'), [
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => '3101234567', // 10 dígitos exactos
        ]);
        $respExact->assertSessionDoesntHaveErrors(['phone']);
        $this->assertEquals('3101234567', $user->fresh()->phone);
    }
}
