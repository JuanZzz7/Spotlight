<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Company, Product, Tag};

class TagAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener tags clave
        $tagFood        = Tag::where('name', 'food')->first();
        $tagBakery      = Tag::where('name', 'bakery')->first();
        $tagClothing    = Tag::where('name', 'clothing')->first();
        $tagElectronics = Tag::where('name', 'electronics')->first();
        $tagArt         = Tag::where('name', 'art')->first();
        $tagHealth      = Tag::where('name', 'health')->first();

        // 2. Asignar tags a productos existentes de TechStore Pro
        $techTags = array_filter([$tagElectronics?->id]);
        foreach ([1, 2, 3, 6, 7, 8, 9] as $pid) {
            $p = Product::find($pid);
            if ($p && !empty($techTags)) {
                $p->tags()->syncWithoutDetaching($techTags);
            }
        }

        // 3. Asignar tags a productos existentes de Moda Elegante
        $p4 = Product::find(4);
        if ($p4 && $tagClothing) {
            $p4->tags()->syncWithoutDetaching([$tagClothing->id]);
        }
        $p5 = Product::find(5);
        if ($p5) {
            $p5Tags = array_filter([$tagClothing?->id, $tagArt?->id]);
            $p5->tags()->syncWithoutDetaching($p5Tags);
        }

        // 4. Crear productos auténticos de Ubaté para Lácteos San Diego si no existen
        $lacteosCompany = Company::where('name', 'like', '%Lácteos%')->first();
        if ($lacteosCompany) {
            $pLact1 = Product::firstOrCreate(
                ['company_id' => $lacteosCompany->id, 'name' => 'Queso Campesino Tradicional de Ubaté'],
                [
                    'description' => 'Elaborado artesanalmente con leche 100% pura del valle de Ubaté, fresco, suave y de receta tradicional campesina.',
                    'price' => 18000,
                    'cost_price' => 11000,
                    'stock' => 35,
                    'active' => true,
                    'featured' => true,
                    'country' => 'CO',
                    'currency' => 'COP',
                    'tax_type' => '01',
                    'tax_rate' => 0.00,
                    'unit_measure' => 'KGM',
                ]
            );
            if ($tagFood) {
                $pLact1->tags()->syncWithoutDetaching([$tagFood->id]);
            }

            $pLact2 = Product::firstOrCreate(
                ['company_id' => $lacteosCompany->id, 'name' => 'Arequipe Artesanal en Paila de Cobre'],
                [
                    'description' => 'Dulce de leche tradicional cocido a fuego lento en paila de cobre, consistencia cremosa inigualable.',
                    'price' => 12500,
                    'cost_price' => 7000,
                    'stock' => 40,
                    'active' => true,
                    'featured' => true,
                    'country' => 'CO',
                    'currency' => 'COP',
                    'tax_type' => '01',
                    'tax_rate' => 19.00,
                    'unit_measure' => '94',
                ]
            );
            $lact2Tags = array_filter([$tagFood?->id, $tagBakery?->id]);
            if (!empty($lact2Tags)) {
                $pLact2->tags()->syncWithoutDetaching($lact2Tags);
            }

            $pLact3 = Product::firstOrCreate(
                ['company_id' => $lacteosCompany->id, 'name' => 'Yogur Griego Natural con Frutos Silvestres'],
                [
                    'description' => 'Yogur de leche entera pasteurizada, rico en probióticos naturales, sin conservantes artificiales.',
                    'price' => 14000,
                    'cost_price' => 8500,
                    'stock' => 25,
                    'active' => true,
                    'featured' => true,
                    'country' => 'CO',
                    'currency' => 'COP',
                    'tax_type' => '01',
                    'tax_rate' => 0.00,
                    'unit_measure' => '94',
                ]
            );
            $lact3Tags = array_filter([$tagFood?->id, $tagHealth?->id]);
            if (!empty($lact3Tags)) {
                $pLact3->tags()->syncWithoutDetaching($lact3Tags);
            }
        }
    }
}
