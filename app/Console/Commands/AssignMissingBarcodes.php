<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class AssignMissingBarcodes extends Command
{
    protected $signature = 'products:assign-barcodes';

    protected $description = 'Assigne un barcode à 5 chiffres aux produits qui n\'en ont pas';

    public function handle(): void
    {
        $products = Product::whereNull('barcode')->orWhere('barcode', '')->get();

        if ($products->isEmpty()) {
            $this->info('Tous les produits ont déjà un barcode.');

            return;
        }

        foreach ($products as $product) {
            do {
                $code = random_int(10000, 99999);
            } while (Product::where('barcode', $code)->exists());

            $product->update(['barcode' => $code]);
            $this->line("Produit #{$product->id} ({$product->name}) → {$code}");
        }

        $this->info("{$products->count()} barcode(s) assigné(s).");
    }
}
