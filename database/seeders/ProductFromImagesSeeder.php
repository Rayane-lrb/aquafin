<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductFromImagesSeeder extends Seeder
{
    public function run(): void
    {
        $sourceDir = public_path('images/material_pics');
        $destDir = storage_path('app/public/products');

        File::ensureDirectoryExists($destDir);

        $images = collect(File::files($sourceDir));

        if ($images->isEmpty()) {
            $this->command->error("Geen afbeeldingen gevonden in: {$sourceDir}");

            return;
        }

        // Toon beschikbare categorieën
        $categories = ProductCategory::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Geen categorieën gevonden. Maak eerst een categorie aan via /productcategory/create.');
            $categoryId = null;

            if ($this->command->confirm('Wil je een standaard categorie "Algemeen" aanmaken?', true)) {
                $categoryId = ProductCategory::create(['name' => 'Algemeen'])->id;
                $this->command->info('Categorie "Algemeen" aangemaakt.');
            } else {
                $this->command->error('Seeder gestopt: geen categorie beschikbaar.');

                return;
            }
        } else {
            $this->command->info('Beschikbare categorieën:');
            foreach ($categories as $cat) {
                $this->command->line("  [{$cat->id}] {$cat->name}");
            }

            $categoryId = $this->command->ask('Geef het ID van de categorie die je wil gebruiken');

            if (! ProductCategory::find($categoryId)) {
                $this->command->error("Categorie ID {$categoryId} bestaat niet.");

                return;
            }
        }

        $created = 0;
        $skipped = 0;

        foreach ($images as $file) {
            $filename = $file->getFilename();
            $productName = $this->prettifyName($file->getFilenameWithoutExtension());

            // Skip als product al bestaat
            if (Product::whereRaw('LOWER(name) = ?', [Str::lower($productName)])->exists()) {
                $this->command->line("  <fg=gray>⏭  {$productName} (bestaat al)</>");
                $skipped++;

                continue;
            }

            // Kopieer afbeelding naar storage
            $destPath = $destDir.DIRECTORY_SEPARATOR.$filename;
            File::copy($file->getPathname(), $destPath);

            Product::create([
                'name' => $productName,
                'stock' => 0,
                'is_active' => true,
                'is_flood_tool' => false,
                'product_category_id' => $categoryId,
                'image' => 'products/'.$filename,
            ]);

            $this->command->line("  <fg=green>✓  {$productName}</>");
            $created++;
        }

        $this->command->newLine();
        $this->command->info("✅ {$created} producten aangemaakt, {$skipped} overgeslagen.");
    }

    /**
     * Zet bestandsnaam om naar leesbare naam.
     * "BoutM8" → "Bout M8"
     * "tie wraps" → "Tie wraps"
     * "WD-40" → "WD-40"
     */
    private function prettifyName(string $filename): string
    {
        // Voeg spatie in voor hoofdletters die volgen op kleine letters of cijfers
        $spaced = preg_replace('/([a-z])([A-Z])/', '$1 $2', $filename);
        $spaced = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $spaced);

        // Eerste letter hoofdletter
        return Str::ucfirst($spaced);
    }
}
