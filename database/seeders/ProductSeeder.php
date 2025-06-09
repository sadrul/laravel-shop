<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Smartphone X',
                'slug' => 'smartphone-x',
                'description' => 'Latest smartphone with advanced features and high-resolution camera.',
                'price' => 699.99,
                'stock' => 50,
                'image' => null,
            ],
            [
                'name' => 'Laptop Pro',
                'slug' => 'laptop-pro',
                'description' => 'Powerful laptop for professionals with high-performance processor.',
                'price' => 1299.99,
                'stock' => 30,
                'image' => null,
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'wireless-headphones',
                'description' => 'Premium noise-cancelling wireless headphones with long battery life.',
                'price' => 199.99,
                'stock' => 100,
                'image' => null,
            ],
            [
                'name' => 'Smart Watch',
                'slug' => 'smart-watch',
                'description' => 'Feature-rich smartwatch with health monitoring and fitness tracking.',
                'price' => 249.99,
                'stock' => 75,
                'image' => null,
            ],
            [
                'name' => 'Tablet Ultra',
                'slug' => 'tablet-ultra',
                'description' => 'Slim and lightweight tablet with high-resolution display.',
                'price' => 499.99,
                'stock' => 40,
                'image' => null,
            ],
            [
                'name' => 'Gaming Console',
                'slug' => 'gaming-console',
                'description' => 'Next-gen gaming console with 4K graphics and immersive gaming experience.',
                'price' => 499.99,
                'stock' => 25,
                'image' => null,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'slug' => 'bluetooth-speaker',
                'description' => 'Portable Bluetooth speaker with premium sound quality.',
                'price' => 79.99,
                'stock' => 150,
                'image' => null,
            ],
            [
                'name' => 'Digital Camera',
                'slug' => 'digital-camera',
                'description' => 'Professional digital camera with 4K video recording capability.',
                'price' => 899.99,
                'stock' => 20,
                'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 