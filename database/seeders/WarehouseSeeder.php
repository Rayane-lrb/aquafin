<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            // ── Antwerpen ────────────────────────────────────
            ['name' => 'RWZI Aartselaar',      'address' => 'Boomsesteenweg 1002, 2610 Wilrijk'],
            ['name' => 'RWZI Herentals',        'address' => 'Biezenweg 1, 2200 Herentals'],
            ['name' => 'RWZI Lier',             'address' => 'Gevaertlaan 1, 2500 Lier'],
            ['name' => 'RWZI Mol',              'address' => 'Rauwelkoven 1, 2400 Mol'],
            ['name' => 'RWZI Turnhout',         'address' => 'Atealaan 4, 2200 Herentals'],

            // ── Oost-Vlaanderen ──────────────────────────────
            ['name' => 'RWZI Gent',             'address' => 'Drongensesteenweg 254, 9000 Gent'],
            ['name' => 'RWZI Aalst',            'address' => 'Spuimeersenweg 2, 9308 Aalst'],
            ['name' => 'RWZI Dendermonde',      'address' => 'Industrielaan 1, 9200 Dendermonde'],
            ['name' => 'RWZI Sint-Niklaas',     'address' => 'Beelbroekstraat 2, 9100 Sint-Niklaas'],

            // ── West-Vlaanderen ──────────────────────────────
            ['name' => 'RWZI Brugge',           'address' => 'Pathoekeweg 45, 8000 Brugge'],
            ['name' => 'RWZI Kortrijk',         'address' => 'Doorniksesteenweg 2, 8500 Kortrijk'],
            ['name' => 'RWZI Roeselare',        'address' => 'Rijksweg 1, 8800 Roeselare'],
            ['name' => 'RWZI Menen',            'address' => 'Industriepark Noord 1, 8930 Menen'],
            ['name' => 'RWZI Harelbeke',        'address' => 'Slijpestraat 1, 8530 Harelbeke'],
            ['name' => 'RWZI Ingelmunster',     'address' => 'Ingelmunstersteenweg 1, 8770 Ingelmunster'],
            ['name' => 'RWZI Heist',            'address' => 'Elisabethlaan 1, 8301 Heist-aan-Zee'],
            ['name' => 'RWZI Knokke',           'address' => 'Kragendijk 1, 8300 Knokke-Heist'],

            // ── Vlaams-Brabant ───────────────────────────────
            ['name' => 'RWZI Leuven',           'address' => 'Aarschotsesteenweg 27, 3010 Kessel-Lo'],
            ['name' => 'RWZI Mechelen',         'address' => 'Tervuursesteenweg 1, 2800 Mechelen'],
            ['name' => 'RWZI Halen',            'address' => 'Industrieweg 1, 3545 Halen'],

            // ── Limburg ──────────────────────────────────────
            ['name' => 'RWZI Hasselt',          'address' => 'Albertkanaalstraat 141, 3511 Hasselt'],
            ['name' => 'RWZI Genk',             'address' => 'Winterslagstraat 1, 3600 Genk'],
            ['name' => 'RWZI Westerlo',         'address' => 'Gevaertlaan 1, 2260 Westerlo'],
            ['name' => 'RWZI Beernem',          'address' => 'Kerkhofstraat 1, 8730 Beernem'],
            ['name' => 'RWZI Nevele',           'address' => 'Stationsstraat 1, 9850 Nevele'],
        ];

        foreach ($locations as $loc) {
            Warehouse::updateOrCreate(
                ['name' => $loc['name']],
                ['address' => $loc['address']]
            );
        }
    }
}
