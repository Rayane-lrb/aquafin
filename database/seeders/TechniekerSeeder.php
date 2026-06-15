<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechniekerSeeder extends Seeder
{
    public function run(): void
    {
        // 1 technieker per RWZI — email: voornaam.achternaam.tech@aquafin.be
        $techniekers = [
            // ── Antwerpen ────────────────────────────────────
            ['rwzi' => 'RWZI Aartselaar',   'name' => 'Lien Vermeersch'],
            ['rwzi' => 'RWZI Herentals',    'name' => 'Pieter Claes'],
            ['rwzi' => 'RWZI Lier',         'name' => 'Sarah Bogaert'],
            ['rwzi' => 'RWZI Mol',          'name' => 'Thomas Janssen'],
            ['rwzi' => 'RWZI Turnhout',     'name' => 'Emma Wouters'],

            // ── Oost-Vlaanderen ──────────────────────────────
            ['rwzi' => 'RWZI Gent',         'name' => 'Kevin Desmet'],
            ['rwzi' => 'RWZI Aalst',        'name' => 'Nathalie Peeters'],
            ['rwzi' => 'RWZI Dendermonde',  'name' => 'Bram Willems'],
            ['rwzi' => 'RWZI Sint-Niklaas', 'name' => 'Elien Goossens'],

            // ── West-Vlaanderen ──────────────────────────────
            ['rwzi' => 'RWZI Brugge',       'name' => 'Jens Maes'],
            ['rwzi' => 'RWZI Kortrijk',     'name' => 'Sofie Declercq'],
            ['rwzi' => 'RWZI Roeselare',    'name' => 'Dries Vandenberghe'],
            ['rwzi' => 'RWZI Menen',        'name' => 'Ann Dewilde'],
            ['rwzi' => 'RWZI Harelbeke',    'name' => 'Sven Leclercq'],
            ['rwzi' => 'RWZI Ingelmunster', 'name' => 'Hilde Vandamme'],
            ['rwzi' => 'RWZI Heist',        'name' => 'Ruben Blomme'],
            ['rwzi' => 'RWZI Knokke',       'name' => 'Laura Debacker'],

            // ── Vlaams-Brabant ───────────────────────────────
            ['rwzi' => 'RWZI Leuven',       'name' => 'Mathias Nijs'],
            ['rwzi' => 'RWZI Mechelen',     'name' => 'Karen Hermans'],
            ['rwzi' => 'RWZI Halen',        'name' => 'Wout Stevens'],

            // ── Limburg ──────────────────────────────────────
            ['rwzi' => 'RWZI Hasselt',      'name' => 'Julie Geerts'],
            ['rwzi' => 'RWZI Genk',         'name' => 'Niels Martens'],
            ['rwzi' => 'RWZI Westerlo',     'name' => 'Amber Michiels'],
            ['rwzi' => 'RWZI Beernem',      'name' => 'Stef Desmedt'],
            ['rwzi' => 'RWZI Nevele',       'name' => 'Inge Claeys'],
        ];

        foreach ($techniekers as $entry) {
            $warehouse = Warehouse::where('name', $entry['rwzi'])->first();

            if (!$warehouse) {
                $this->command->warn("Magazijn niet gevonden: {$entry['rwzi']} — overgeslagen.");
                continue;
            }

            // voornaam.achternaam.tech@aquafin.be
            [$voornaam, $achternaam] = explode(' ', strtolower($entry['name']), 2);
            $achternaam = str_replace(' ', '', $achternaam); // remove spaces in compound names
            $email = "{$voornaam}.{$achternaam}.tech@aquafin.be";

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'                 => $entry['name'],
                    'role'                 => 'technieker',
                    'password'             => Hash::make('password'),
                    'default_warehouse_id' => $warehouse->id,
                ]
            );

            $this->command->info("✓ {$entry['name']} ({$email}) → {$entry['rwzi']}");
        }
    }
}
