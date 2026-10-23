<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MexicanBusinessSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear o recuperar el usuario comerciante mexicano
        $merchant = User::updateOrCreate(
            ['email' => 'alejandro@spotlight.mx'],
            [
                'name'              => 'Alejandro Morales',
                'password'          => Hash::make('password'),
                'role'              => 'business',
                'country'           => 'MX',
                'state'             => 'Puebla',
                'city'              => 'Puebla',
                'postal_code'       => '72000',
                'address'           => 'Av. Juan de Palafox y Mendoza #204, Centro Histórico',
                'phone'             => '2221234567',
                'phone_prefix'      => '+52',
                'email_verified_at' => now(),
            ]
        );

        // 2. Crear o recuperar la empresa mexicana
        $company = Company::updateOrCreate(
            ['user_id' => $merchant->id],
            [
                'name'              => 'Talavera y Cerámica Tradicional de Puebla',
                'description'       => 'Taller artesanal mexicano dedicado al diseño y elaboración tradicional de piezas de talavera certificada, vajillas decorativas y azulejos artesanales hechos a mano.',
                'category'          => 'Hogar',
                'status'            => 'active',
                'country'           => 'MX',
                'state'             => 'Puebla',
                'city'              => 'Puebla',
                'postal_code'       => '72000',
                'address'           => 'Av. Juan de Palafox y Mendoza #204, Centro Histórico',
                'currency'          => 'MXN',
                'phone'             => '2221234567',
                'phone_prefix'      => '+52',
                'email'             => 'talavera@spotlight.mx',
                'tax_id'            => 'MOAJ880101XYZ',
                'tipo_negocio'      => 'informal',
                'latitude'          => 19.0433,
                'longitude'         => -98.1983,
            ]
        );

        // 3. Crear productos en pesos mexicanos (MXN)
        $products = [
            [
                'name'        => 'Jarrón Tradicional de Talavera Poblana',
                'price'       => 650.00,
                'description' => 'Hermoso jarrón decorativo elaborado a mano por artesanos poblanos con esmaltado tradicional azul y blanco.',
                'stock'       => 12,
                'active'      => true,
                'featured'    => true,
            ],
            [
                'name'        => 'Platón Decorativo de Talavera Artesanal',
                'price'       => 480.00,
                'description' => 'Platón circular pintado a mano con motivos florales coloniales de Puebla.',
                'stock'       => 15,
                'active'      => true,
                'featured'    => true,
            ],
            [
                'name'        => 'Juego de 4 Tazas Artesanales de Cerámica',
                'price'       => 320.00,
                'description' => 'Set de 4 tazas de café resistentes al calor con acabado esmaltado artesanal mexicano.',
                'stock'       => 20,
                'active'      => true,
                'featured'    => false,
            ],
        ];

        foreach ($products as $pData) {
            Product::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'name'       => $pData['name'],
                ],
                [
                    'description' => $pData['description'],
                    'price'       => $pData['price'],
                    'stock'       => $pData['stock'],
                    'active'      => $pData['active'],
                    'featured'    => $pData['featured'],
                    'country'     => 'MX',
                    'currency'    => 'MXN',
                ]
            );
        }
    }
}
