<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     */
    public function run(): void
    {
        // 1. Seed Admin Users
        User::create([
            'name' => 'Kanhaiya Rathaur (Admin)',
            'email' => 'kanhaiyarathaur0001@gmail.com',
            'password' => Hash::make('Neha#145'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // 2. Seed Vendors
        $vendor1 = User::create([
            'name' => 'Apex Electronics',
            'email' => 'vendor@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'status' => 'active',
        ]);

        VendorProfile::create([
            'user_id' => $vendor1->id,
            'store_name' => 'Apex Tech Store',
            'store_slug' => 'apex-tech-store',
            'description' => 'Premium gadgets, smartphones, audio devices, and computer peripherals.',
            'phone' => '+91 9876543210',
            'address' => 'Building 4, Electronic City, Bengaluru, Karnataka',
            'status' => 'approved',
        ]);

        $vendor2 = User::create([
            'name' => 'Urban Lifestyle',
            'email' => 'vendor2@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'status' => 'active',
        ]);

        VendorProfile::create([
            'user_id' => $vendor2->id,
            'store_name' => 'Urban Fashion Hub',
            'store_slug' => 'urban-fashion-hub',
            'description' => 'Modern clothing, footwear, and stylish leather accessories.',
            'phone' => '+91 9876543211',
            'address' => 'Shop 12, Fashion Street, Mumbai, Maharashtra',
            'status' => 'approved',
        ]);

        // Blocked vendor for testing
        $blockedVendor = User::create([
            'name' => 'Blocked Vendor',
            'email' => 'blocked_vendor@example.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'status' => 'blocked',
        ]);

        VendorProfile::create([
            'user_id' => $blockedVendor->id,
            'store_name' => 'Blocked Store',
            'store_slug' => 'blocked-store',
            'description' => 'This vendor has been blocked.',
            'status' => 'blocked',
        ]);

        // 3. Seed Customers
        $customer1 = User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Address::create([
            'user_id' => $customer1->id,
            'name' => 'John Customer',
            'phone' => '9876543212',
            'address' => 'Flat 201, Green Meadows, MG Road',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'postal_code' => '560001',
            'country' => 'India',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $customer1->id,
            'name' => 'John (Office)',
            'phone' => '9876543212',
            'address' => 'Tech Park, Floor 3, Whitefield',
            'city' => 'Bengaluru',
            'state' => 'Karnataka',
            'postal_code' => '560066',
            'country' => 'India',
            'is_default' => false,
        ]);

        $customer2 = User::create([
            'name' => 'Sarah Connor',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
        ]);

        Address::create([
            'user_id' => $customer2->id,
            'name' => 'Sarah Connor',
            'phone' => '9876543213',
            'address' => '45 Hilltop Villa',
            'city' => 'Pune',
            'state' => 'Maharashtra',
            'postal_code' => '411001',
            'country' => 'India',
            'is_default' => true,
        ]);

        // Blocked customer
        User::create([
            'name' => 'Blocked Customer',
            'email' => 'blocked_customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'blocked',
        ]);

        for ($i = 3; $i <= 6; $i++) {
            User::create([
                'name' => "Customer User {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);
        }

        // 4. Seed Categories
        $categoriesData = [
            ['name' => 'Smartphones & Tablets', 'description' => 'Mobile phones, tablets and accessories.'],
            ['name' => 'Computers & Laptops', 'description' => 'Laptops, desktop PCs, monitors and PC parts.'],
            ['name' => 'Audio & Headphones', 'description' => 'Earbuds, noise-canceling headphones, and wireless speakers.'],
            ['name' => 'Wearables & Smartwatches', 'description' => 'Fitness bands and connected smartwatches.'],
            ['name' => 'Clothing & Fashion', 'description' => 'Men and women apparel, jackets and t-shirts.'],
            ['name' => 'Accessories & Leather', 'description' => 'Wallets, bags, belts and stylish accessories.'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $cat = Category::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'status' => 'active',
            ]);
            $categories[] = $cat;
        }

        // 5. Seed Products with Sample Images
        $productsData = [
            // Vendor 1 (Apex Tech)
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[0]->id,
                'name' => 'Galaxy Nova 5G Smartphone',
                'price' => 34999.00,
                'stock' => 15,
                'sku' => 'APEX-PHONE-01',
                'description' => 'Flagship smartphone featuring AMOLED 120Hz display, 108MP triple camera system, and ultra-fast 67W charging.',
                'image' => 'products/phone.jpg',
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[1]->id,
                'name' => 'ProBook Ultra 15 Laptop',
                'price' => 64999.00,
                'stock' => 8,
                'sku' => 'APEX-LAPTOP-01',
                'description' => 'Thin and light notebook equipped with Core i7 processor, 16GB RAM, 512GB NVMe SSD, and 14-hour battery life.',
                'image' => 'products/laptop.jpg',
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[2]->id,
                'name' => 'SonicAir Active Noise Canceling Headphones',
                'price' => 7499.00,
                'stock' => 25,
                'sku' => 'APEX-HEADPHONE-01',
                'description' => 'Wireless over-ear headphones with 40mm neodymium drivers, dual microphones, and 30-hour playback.',
                'image' => 'products/headphones.jpg',
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[3]->id,
                'name' => 'FitPulse Tracker Smart Watch',
                'price' => 3999.00,
                'stock' => 30,
                'sku' => 'APEX-WATCH-01',
                'description' => 'Smart health tracker with continuous heart rate monitoring, SpO2 sensor, sleep tracking, and IP68 water resistance.',
                'image' => 'products/watch.jpg',
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[1]->id,
                'name' => 'MechKey RGB Mechanical Keyboard',
                'price' => 4599.00,
                'stock' => 20,
                'sku' => 'APEX-KEYBOARD-01',
                'description' => 'Tactile blue switch mechanical keyboard with customizable RGB backlighting and durable aluminum frame.',
                'image' => 'products/keyboard.jpg',
            ],
            [
                'vendor_id' => $vendor1->id,
                'category_id' => $categories[1]->id,
                'name' => 'Precision Pro Wireless Gaming Mouse',
                'price' => 1899.00,
                'stock' => 40,
                'sku' => 'APEX-MOUSE-01',
                'description' => 'Ergonomic gaming mouse with 16000 DPI optical sensor, ultra-light design, and 1ms low-latency wireless connection.',
                'image' => 'products/mouse.jpg',
            ],
            // Vendor 2 (Urban Fashion)
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[4]->id,
                'name' => 'Classic Oxford Cotton T-Shirt',
                'price' => 899.00,
                'stock' => 50,
                'sku' => 'URBAN-TSHIRT-01',
                'description' => '100% combed cotton breathable crewneck t-shirt with premium stitched finish.',
                'image' => 'products/tshirt.jpg',
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[5]->id,
                'name' => 'Vintage Handcrafted Leather Wallet',
                'price' => 1299.00,
                'stock' => 25,
                'sku' => 'URBAN-WALLET-01',
                'description' => 'Genuine top-grain leather bi-fold wallet featuring RFID protection and 8 card slots.',
                'image' => 'products/wallet.jpg',
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[4]->id,
                'name' => 'Urban Denim Casual Jacket',
                'price' => 2999.00,
                'stock' => 12,
                'sku' => 'URBAN-JACKET-01',
                'description' => 'Classic stone-washed denim jacket with metallic buttons and dual chest pockets.',
                'image' => 'products/jacket.jpg',
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[5]->id,
                'name' => 'Executive Leather Messenger Bag',
                'price' => 3899.00,
                'stock' => 10,
                'sku' => 'URBAN-BAG-01',
                'description' => 'Spacious leather laptop messenger bag with padded 15.6 inch laptop sleeve and adjustable shoulder strap.',
                'image' => 'products/bag.jpg',
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[4]->id,
                'name' => 'Slim Fit Chino Trousers',
                'price' => 1799.00,
                'stock' => 22,
                'sku' => 'URBAN-CHINO-01',
                'description' => 'Modern stretch cotton chinos suitable for both business casual workwear and weekend outings.',
                'image' => 'products/chino.jpg',
            ],
            [
                'vendor_id' => $vendor2->id,
                'category_id' => $categories[2]->id,
                'name' => 'BassPro Wireless Bluetooth Speaker',
                'price' => 2499.00,
                'stock' => 18,
                'sku' => 'APEX-SPEAKER-01',
                'description' => 'Portable waterproof Bluetooth speaker with 360-degree sound and 12-hour continuous battery life.',
                'image' => 'products/speaker.jpg',
            ],
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $imageFile = $data['image'];
            unset($data['image']);

            $product = Product::create(array_merge($data, [
                'slug' => Str::slug($data['name']).'-'.Str::random(5),
                'status' => 'active',
            ]));

            // Add primary product image
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $imageFile,
                'is_primary' => true,
            ]);

            $createdProducts[] = $product;
        }

        // 6. Seed Sample Order for Customer 1 (so they can review products)
        $orderProduct1 = $createdProducts[0]; // Galaxy Nova
        $orderProduct2 = $createdProducts[2]; // SonicAir Headphones

        $orderSubtotal = ($orderProduct1->price * 1) + ($orderProduct2->price * 1);
        $orderShipping = 50.00;
        $orderTotal = $orderSubtotal + $orderShipping;

        $sampleOrder = Order::create([
            'user_id' => $customer1->id,
            'order_number' => 'ORD-DEMO12345',
            'subtotal' => $orderSubtotal,
            'shipping_fee' => $orderShipping,
            'discount' => 0.00,
            'total' => $orderTotal,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'shipping_address' => "John Customer, 9876543212\nFlat 201, Green Meadows, MG Road, Bengaluru, Karnataka - 560001, India",
        ]);

        OrderItem::create([
            'order_id' => $sampleOrder->id,
            'product_id' => $orderProduct1->id,
            'vendor_id' => $orderProduct1->vendor_id,
            'product_name' => $orderProduct1->name,
            'price' => $orderProduct1->price,
            'quantity' => 1,
            'total' => $orderProduct1->price,
        ]);

        OrderItem::create([
            'order_id' => $sampleOrder->id,
            'product_id' => $orderProduct2->id,
            'vendor_id' => $orderProduct2->vendor_id,
            'product_name' => $orderProduct2->name,
            'price' => $orderProduct2->price,
            'quantity' => 1,
            'total' => $orderProduct2->price,
        ]);

        Payment::create([
            'order_id' => $sampleOrder->id,
            'transaction_id' => 'ch_demo_1234567890',
            'amount' => $orderTotal,
            'method' => 'stripe',
            'status' => 'paid',
            'paid_at' => now()->subDays(2),
        ]);

        // 7. Seed Sample Reviews
        Review::create([
            'user_id' => $customer1->id,
            'product_id' => $orderProduct1->id,
            'order_id' => $sampleOrder->id,
            'rating' => 5,
            'comment' => 'Outstanding smartphone! Display is crystal clear and battery easily lasts all day.',
            'status' => 'approved',
        ]);

        Review::create([
            'user_id' => $customer1->id,
            'product_id' => $orderProduct2->id,
            'order_id' => $sampleOrder->id,
            'rating' => 4,
            'comment' => 'Great sound quality and comfortable over the ears. Noise cancellation is very effective.',
            'status' => 'approved',
        ]);
    }
}
