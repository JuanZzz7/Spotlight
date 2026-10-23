<?php

namespace Database\Seeders;

use App\Models\{User, Company, Product, Order, OrderDetail, Expense};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@spotlight.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Empresarios
        $b1 = User::create(['name' => 'Carlos Empresario', 'email' => 'carlos@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'business']);
        $b2 = User::create(['name' => 'María Empresaria', 'email' => 'maria@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'business']);

        // Usuarios
        $u1 = User::create(['name' => 'Ana García',   'email' => 'ana@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'user']);
        $u2 = User::create(['name' => 'Luis Pérez',   'email' => 'luis@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'user']);
        $u3 = User::create(['name' => 'Sofia Torres', 'email' => 'sofia@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'user']);
        $u4 = User::create(['name' => 'Pedro Ruiz',   'email' => 'pedro@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'user']);
        $u5 = User::create(['name' => 'Laura Gómez',  'email' => 'laura@spotlight.com',
            'password' => Hash::make('password'), 'role' => 'user']);

        // Empresas
        $c1 = Company::create([
            'user_id'              => $b1->id,
            'name'                 => 'TechStore Pro',
            'description'          => 'Soluciones tecnológicas, equipos informáticos y accesorios.',
            'category'             => 'Tecnología',
            'tipo_negocio'         => 'formal',
            'razon_social'         => 'TechStore Pro Colombia S.A.S.',
            'nit'                  => '901834521-4',
            'nombre_representante' => 'Carlos Eduardo Mendoza',
            'cedula_propietario'   => '79845120',
            'email_representante'  => 'gerencia@techstore.com',
            'address'              => 'Carrera 7 # 8-45, Villa de San Diego de Ubaté',
            'phone'                => '3101234567',
            'email'                => 'techstore@spotlight.com',
            'status'               => 'active',
            'country'              => 'CO',
            'latitude'             => 5.3086,
            'longitude'            => -73.8149,
        ]);
        $c2 = Company::create([
            'user_id'              => $b2->id,
            'name'                 => 'Moda Elegante',
            'description'          => 'Taller artesanal de ropa y accesorios de moda en Ubaté.',
            'category'             => 'Moda',
            'tipo_negocio'         => 'informal',
            'address'              => 'Carrera 7 # 12-45, Ubaté',
            'phone'                => '3109876543',
            'email'                => 'moda@spotlight.com',
            'status'               => 'active',
            'country'              => 'CO',
            'latitude'             => 5.3100,
            'longitude'            => -73.8160,
        ]);

        // Productos
        $p1 = Product::create(['company_id' => $c1->id, 'name' => 'Audífonos Bluetooth',
            'description' => 'Audífonos inalámbricos con cancelación de ruido', 'price' => 150000,
            'stock' => 20, 'active' => true, 'featured' => true]);
        $p2 = Product::create(['company_id' => $c1->id, 'name' => 'Mouse Gamer RGB',
            'description' => 'Mouse gaming con 7 botones programables', 'price' => 85000,
            'stock' => 15, 'active' => true, 'featured' => true]);
        $p3 = Product::create(['company_id' => $c1->id, 'name' => 'Teclado Mecánico',
            'description' => 'Teclado mecánico retroiluminado', 'price' => 220000,
            'stock' => 10, 'active' => true, 'featured' => false]);
        $p4 = Product::create(['company_id' => $c2->id, 'name' => 'Camiseta Premium',
            'description' => 'Camiseta 100% algodón colección 2024', 'price' => 45000,
            'stock' => 50, 'active' => true, 'featured' => true]);
        $p5 = Product::create(['company_id' => $c2->id, 'name' => 'Bolso de Cuero',
            'description' => 'Bolso artesanal de cuero genuino', 'price' => 180000,
            'stock' => 8, 'active' => true, 'featured' => true]);

        // Órdenes de ejemplo
        $order1 = Order::create(['user_id' => $u1->id, 'total' => 235000, 'status' => 'paid']);
        OrderDetail::create(['order_id' => $order1->id, 'product_id' => $p1->id,
            'quantity' => 1, 'unit_price' => 150000, 'subtotal' => 150000]);
        OrderDetail::create(['order_id' => $order1->id, 'product_id' => $p2->id,
            'quantity' => 1, 'unit_price' => 85000, 'subtotal' => 85000]);

        $order2 = Order::create(['user_id' => $u2->id, 'total' => 45000, 'status' => 'delivered']);
        OrderDetail::create(['order_id' => $order2->id, 'product_id' => $p4->id,
            'quantity' => 1, 'unit_price' => 45000, 'subtotal' => 45000]);

        // Egresos
        Expense::create(['company_id' => $c1->id, 'concept' => 'Publicidad Instagram',
            'amount' => 50000, 'date' => now()->subDays(5), 'description' => 'Pauta pagada']);
        Expense::create(['company_id' => $c2->id, 'concept' => 'Arriendo local',
            'amount' => 800000, 'date' => now()->subDays(10), 'description' => 'Pago mensual']);

        // Seeder especializado para Facturación Electrónica DIAN y usuario de empresa formal
        $this->call(FormalBusinessBillingSeeder::class);

        // Seeder con orden y guía de prueba para rastreo logístico en tiempo real
        $this->call(TestShipmentSeeder::class);

        // Seeder con empresa y productos de México (Marketplace Multinacional CO/MX)
        $this->call(MexicanBusinessSeeder::class);
    }
}
