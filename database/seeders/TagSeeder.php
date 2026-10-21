<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'food', 'label' => 'Comida y bebida', 'icon' => 'bi-cup-hot', 'theme' => ['accent' => '#f97316']],
            ['name' => 'bakery', 'label' => 'Panadería', 'icon' => 'bi-cake2', 'theme' => ['accent' => '#d97706']],
            ['name' => 'clothing', 'label' => 'Ropa y moda', 'icon' => 'bi-bag-heart', 'theme' => ['accent' => '#ec4899']],
            ['name' => 'footwear', 'label' => 'Calzado', 'icon' => 'bi-boot', 'theme' => ['accent' => '#8b5cf6']],
            ['name' => 'electronics', 'label' => 'Tecnología', 'icon' => 'bi-cpu', 'theme' => ['accent' => '#0ea5e9']],
            ['name' => 'home', 'label' => 'Hogar y deco', 'icon' => 'bi-house-heart', 'theme' => ['accent' => '#10b981']],
            ['name' => 'beauty', 'label' => 'Belleza', 'icon' => 'bi-stars', 'theme' => ['accent' => '#f43f5e']],
            ['name' => 'health', 'label' => 'Salud y farmacia', 'icon' => 'bi-capsule', 'theme' => ['accent' => '#22c55e']],
            ['name' => 'sports', 'label' => 'Deporte', 'icon' => 'bi-trophy', 'theme' => ['accent' => '#3b82f6']],
            ['name' => 'toys', 'label' => 'Juguetes', 'icon' => 'bi-balloon', 'theme' => ['accent' => '#f59e0b']],
            ['name' => 'books', 'label' => 'Libros y papelería', 'icon' => 'bi-book', 'theme' => ['accent' => '#6366f1']],
            ['name' => 'pets', 'label' => 'Mascotas', 'icon' => 'bi-heart', 'theme' => ['accent' => '#f97316']],
            ['name' => 'auto', 'label' => 'Automotriz', 'icon' => 'bi-car-front', 'theme' => ['accent' => '#64748b']],
            ['name' => 'agriculture', 'label' => 'Agricultura', 'icon' => 'bi-flower1', 'theme' => ['accent' => '#16a34a']],
            ['name' => 'construction', 'label' => 'Construcción', 'icon' => 'bi-hammer', 'theme' => ['accent' => '#78716c']],
            ['name' => 'services', 'label' => 'Servicios', 'icon' => 'bi-tools', 'theme' => ['accent' => '#0d9488']],
            ['name' => 'education', 'label' => 'Educación', 'icon' => 'bi-mortarboard', 'theme' => ['accent' => '#7c3aed']],
            ['name' => 'art', 'label' => 'Arte y artesanía', 'icon' => 'bi-palette', 'theme' => ['accent' => '#e11d48']],
            ['name' => 'music', 'label' => 'Música', 'icon' => 'bi-music-note-beamed', 'theme' => ['accent' => '#0d9488']],
            ['name' => 'travel', 'label' => 'Turismo', 'icon' => 'bi-airplane', 'theme' => ['accent' => '#0ea5e9']],
            ['name' => 'events', 'label' => 'Eventos', 'icon' => 'bi-balloon-heart', 'theme' => ['accent' => '#a855f7']],
            ['name' => 'wholesale', 'label' => 'Mayorista', 'icon' => 'bi-boxes', 'theme' => ['accent' => '#00236f']],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                [
                    'label' => $tag['label'],
                    'icon' => $tag['icon'],
                    'theme' => $tag['theme']
                ]
            );
        }
    }
}
