<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $sourceDir = public_path('images/material_pics');
        $destDir   = storage_path('app/public/products');
        File::ensureDirectoryExists($destDir);

        // ── Categorieën aanmaken ──────────────────────────────────────
        $cats = collect([
            'Bouten & Schroeven',
            'Moeren & Ringen',
            'Draadstangen',
            'Leidingen & Koppelingen',
            'Gereedschap & Machines',
            'Inspectie & Meting',
            'Verbruiksmaterialen',
            'Elektrisch',
            'Aandrijving',
        ])->mapWithKeys(function ($name) {
            return [$name => ProductCategory::firstOrCreate(['name' => $name])->id];
        });

        // ── Product → Categorie mapping ───────────────────────────────
        $mapping = [
            // Bouten & Schroeven
            'BoutA2'            => 'Bouten & Schroeven',
            'BoutA4'            => 'Bouten & Schroeven',
            'BoutM6'            => 'Bouten & Schroeven',
            'BoutM8'            => 'Bouten & Schroeven',
            'BoutM10'           => 'Bouten & Schroeven',
            'BoutM12'           => 'Bouten & Schroeven',
            'BoutM16'           => 'Bouten & Schroeven',
            'Tabbout'           => 'Bouten & Schroeven',
            'ankerbout'         => 'Bouten & Schroeven',
            'inbusbout'         => 'Bouten & Schroeven',
            'kleibout'          => 'Bouten & Schroeven',
            'zeskantkopbout'    => 'Bouten & Schroeven',
            'Spaanplaatschroef' => 'Bouten & Schroeven',
            'Torx-schroef'      => 'Bouten & Schroeven',
            'kruiskopschroef'   => 'Bouten & Schroeven',
            'parkervijs'        => 'Bouten & Schroeven',
            'zelftappendevijs'  => 'Bouten & Schroeven',

            // Moeren & Ringen
            'borgmoeren'        => 'Moeren & Ringen',
            'flensmoeren'       => 'Moeren & Ringen',
            'inslagmoeren'      => 'Moeren & Ringen',
            'zeskantmoeren'     => 'Moeren & Ringen',
            'sluitring'         => 'Moeren & Ringen',
            'tandveerring'      => 'Moeren & Ringen',
            'veerring'          => 'Moeren & Ringen',

            // Draadstangen
            'draadstangM6'      => 'Draadstangen',
            'draadstangM8'      => 'Draadstangen',
            'draadstangM10'     => 'Draadstangen',
            'draadstangM12'     => 'Draadstangen',
            'draadstangM16'     => 'Draadstangen',

            // Leidingen & Koppelingen
            'afvoerleidingssysteem'   => 'Leidingen & Koppelingen',
            'drukleidingssysteem'     => 'Leidingen & Koppelingen',
            'gardenakoppeling'        => 'Leidingen & Koppelingen',
            'geka koppeling'          => 'Leidingen & Koppelingen',
            'Pneumatische koppelingen'=> 'Leidingen & Koppelingen',
            'rioolstop'               => 'Leidingen & Koppelingen',
            'slangenwagen'            => 'Leidingen & Koppelingen',

            // Gereedschap & Machines
            'HILTI'             => 'Gereedschap & Machines',
            'hogedrukreiniger'  => 'Gereedschap & Machines',
            'mangatopener'      => 'Gereedschap & Machines',
            'putdekselhaak'     => 'Gereedschap & Machines',
            'Ontstoppingsveer'  => 'Gereedschap & Machines',
            'dompelpomp'        => 'Gereedschap & Machines',

            // Inspectie & Meting
            'radar niveaumeter'       => 'Inspectie & Meting',
            'ultrasone niveaumeter'   => 'Inspectie & Meting',
            'gasdetectietoestel'      => 'Inspectie & Meting',
            'monstername-apparatuur'  => 'Inspectie & Meting',
            'Staalnamepot'            => 'Inspectie & Meting',
            'inspectiecamera'         => 'Inspectie & Meting',
            'rioolcamera'             => 'Inspectie & Meting',

            // Verbruiksmaterialen
            'WD-40'             => 'Verbruiksmaterialen',
            'contactspray'      => 'Verbruiksmaterialen',
            'kettingspray'      => 'Verbruiksmaterialen',
            'ducttape'          => 'Verbruiksmaterialen',
            'isolatietape'      => 'Verbruiksmaterialen',
            'markeringstape'    => 'Verbruiksmaterialen',
            'siliconenkit'      => 'Verbruiksmaterialen',
            'repair lijm'       => 'Verbruiksmaterialen',
            'rags'              => 'Verbruiksmaterialen',
            'FlesPerslucht'     => 'Verbruiksmaterialen',
            'tie wraps'         => 'Verbruiksmaterialen',

            // Elektrisch
            'batterijaccu'      => 'Elektrisch',
            'aansluitdoos'      => 'Elektrisch',
            'voltterschakelaar' => 'Elektrisch',
            'kabelschoenen'     => 'Elektrisch',

            // Aandrijving
            'kettingen'         => 'Aandrijving',
            'v-snaren'          => 'Aandrijving',
            'trillingsdemper'   => 'Aandrijving',
        ];

        $created = 0;
        $skipped = 0;

        foreach ($mapping as $filename => $categoryName) {
            $imagePath = $sourceDir . '/' . $filename . '.png';

            if (! File::exists($imagePath)) {
                $this->command->line("  <fg=yellow>⚠  Afbeelding niet gevonden: {$filename}.png</>");
                continue;
            }

            $productName = $this->prettifyName($filename);

            if (Product::whereRaw('LOWER(name) = ?', [strtolower($productName)])->exists()) {
                $this->command->line("  <fg=gray>⏭  {$productName} (bestaat al)</>");
                $skipped++;
                continue;
            }

            // Kopieer afbeelding naar storage
            $destFile = $destDir . '/' . $filename . '.png';
            File::copy($imagePath, $destFile);

            Product::create([
                'name'                => $productName,
                'stock'               => 0,
                'is_active'           => true,
                'is_flood_tool'       => false,
                'product_category_id' => $cats[$categoryName],
                'image'               => 'products/' . $filename . '.png',
            ]);

            $this->command->line("  <fg=green>✓  {$productName} [{$categoryName}]</>");
            $created++;
        }

        $this->command->newLine();
        $this->command->info("✅ {$created} producten aangemaakt, {$skipped} overgeslagen.");
    }

    private function prettifyName(string $filename): string
    {
        $spaced = preg_replace('/([a-z])([A-Z])/', '$1 $2', $filename);
        $spaced = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $spaced);
        return ucfirst($spaced);
    }
}
