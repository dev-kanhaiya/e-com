<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class DownloadProductImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'download:product-images';

    /**
     * The console command description.
     */
    protected $description = 'Download placeholder images for products and store them in storage/app/public/products';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Ensure the storage directory exists
        Storage::makeDirectory('public/products');

        $products = Product::all();
        foreach ($products as $product) {
            // Generate a placeholder image URL based on product name using picsum.photos
            $seed = urlencode($product->name);
            $url = "https://picsum.photos/seed/{$seed}/800/800";

            $this->info("Downloading image for {$product->name} from {$url}");
            $imageContents = @file_get_contents($url);
            if ($imageContents === false) {
                $this->error("Failed to download image for {$product->name}");
                continue;
            }

            $filename = strtolower(str_replace(' ', '_', $product->slug)) . '.jpg';
            $path = "public/products/{$filename}";
            Storage::put($path, $imageContents);

            // Update or create product image record
            $existing = ProductImage::where('product_id', $product->id)->first();
            if ($existing) {
                $existing->image = "products/{$filename}";
                $existing->is_primary = true;
                $existing->save();
            } else {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => "products/{$filename}",
                    'is_primary' => true,
                ]);
            }
        }

        $this->info('All product images downloaded and stored.');
        return 0;
    }
}
?>
