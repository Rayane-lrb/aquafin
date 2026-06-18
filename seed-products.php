<?php
/**
 * seed-products.php — insère catégories + produits dans la DB SQLite
 * Lance avec : php seed-products.php
 */

$dbPath  = __DIR__ . '/database/database.sqlite';
$srcDir  = __DIR__ . '/public/images/material_pics';
$destDir = __DIR__ . '/storage/app/public/products';

if (! is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("❌ Kon DB niet openen: " . $e->getMessage() . "\n");
}

$now = date('Y-m-d H:i:s');

// ── Categorieën ──────────────────────────────────────────────────────────────
$categories = [
    'Bouten & Schroeven', 'Moeren & Ringen', 'Draadstangen',
    'Leidingen & Koppelingen', 'Gereedschap & Machines',
    'Inspectie & Meting', 'Verbruiksmaterialen', 'Elektrisch', 'Aandrijving',
];

$catIds = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT id FROM product_categories WHERE name = ?");
    $stmt->execute([$cat]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $catIds[$cat] = $row['id'];
    } else {
        $ins = $pdo->prepare("INSERT INTO product_categories (name, created_at, updated_at) VALUES (?, ?, ?)");
        $ins->execute([$cat, $now, $now]);
        $catIds[$cat] = $pdo->lastInsertId();
        echo "✓ Categorie aangemaakt: $cat\n";
    }
}

// ── Product → Categorie mapping ──────────────────────────────────────────────
$mapping = [
    'BoutA2'                   => 'Bouten & Schroeven',
    'BoutA4'                   => 'Bouten & Schroeven',
    'BoutM6'                   => 'Bouten & Schroeven',
    'BoutM8'                   => 'Bouten & Schroeven',
    'BoutM10'                  => 'Bouten & Schroeven',
    'BoutM12'                  => 'Bouten & Schroeven',
    'BoutM16'                  => 'Bouten & Schroeven',
    'Tabbout'                  => 'Bouten & Schroeven',
    'ankerbout'                => 'Bouten & Schroeven',
    'inbusbout'                => 'Bouten & Schroeven',
    'kleibout'                 => 'Bouten & Schroeven',
    'zeskantkopbout'           => 'Bouten & Schroeven',
    'Spaanplaatschroef'        => 'Bouten & Schroeven',
    'Torx-schroef'             => 'Bouten & Schroeven',
    'kruiskopschroef'          => 'Bouten & Schroeven',
    'parkervijs'               => 'Bouten & Schroeven',
    'zelftappendevijs'         => 'Bouten & Schroeven',
    'borgmoeren'               => 'Moeren & Ringen',
    'flensmoeren'              => 'Moeren & Ringen',
    'inslagmoeren'             => 'Moeren & Ringen',
    'zeskantmoeren'            => 'Moeren & Ringen',
    'sluitring'                => 'Moeren & Ringen',
    'tandveerring'             => 'Moeren & Ringen',
    'veerring'                 => 'Moeren & Ringen',
    'draadstangM6'             => 'Draadstangen',
    'draadstangM8'             => 'Draadstangen',
    'draadstangM10'            => 'Draadstangen',
    'draadstangM12'            => 'Draadstangen',
    'draadstangM16'            => 'Draadstangen',
    'afvoerleidingssysteem'    => 'Leidingen & Koppelingen',
    'drukleidingssysteem'      => 'Leidingen & Koppelingen',
    'gardenakoppeling'         => 'Leidingen & Koppelingen',
    'geka koppeling'           => 'Leidingen & Koppelingen',
    'Pneumatische koppelingen' => 'Leidingen & Koppelingen',
    'rioolstop'                => 'Leidingen & Koppelingen',
    'slangenwagen'             => 'Leidingen & Koppelingen',
    'HILTI'                    => 'Gereedschap & Machines',
    'hogedrukreiniger'         => 'Gereedschap & Machines',
    'mangatopener'             => 'Gereedschap & Machines',
    'putdekselhaak'            => 'Gereedschap & Machines',
    'Ontstoppingsveer'         => 'Gereedschap & Machines',
    'dompelpomp'               => 'Gereedschap & Machines',
    'radar niveaumeter'        => 'Inspectie & Meting',
    'ultrasone niveaumeter'    => 'Inspectie & Meting',
    'gasdetectietoestel'       => 'Inspectie & Meting',
    'monstername-apparatuur'   => 'Inspectie & Meting',
    'Staalnamepot'             => 'Inspectie & Meting',
    'inspectiecamera'          => 'Inspectie & Meting',
    'rioolcamera'              => 'Inspectie & Meting',
    'WD-40'                    => 'Verbruiksmaterialen',
    'contactspray'             => 'Verbruiksmaterialen',
    'kettingspray'             => 'Verbruiksmaterialen',
    'ducttape'                 => 'Verbruiksmaterialen',
    'isolatietape'             => 'Verbruiksmaterialen',
    'markeringstape'           => 'Verbruiksmaterialen',
    'siliconenkit'             => 'Verbruiksmaterialen',
    'repair lijm'              => 'Verbruiksmaterialen',
    'rags'                     => 'Verbruiksmaterialen',
    'FlesPerslucht'            => 'Verbruiksmaterialen',
    'tie wraps'                => 'Verbruiksmaterialen',
    'batterijaccu'             => 'Elektrisch',
    'aansluitdoos'             => 'Elektrisch',
    'voltterschakelaar'        => 'Elektrisch',
    'kabelschoenen'            => 'Elektrisch',
    'kettingen'                => 'Aandrijving',
    'v-snaren'                 => 'Aandrijving',
    'trillingsdemper'          => 'Aandrijving',
];

function prettifyName(string $filename): string {
    $s = preg_replace('/([a-z])([A-Z])/', '$1 $2', $filename);
    $s = preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $s);
    return ucfirst($s);
}

$created = 0;
$skipped = 0;
$missing = 0;

foreach ($mapping as $filename => $categoryName) {
    $srcFile = $srcDir . '/' . $filename . '.png';

    if (! file_exists($srcFile)) {
        echo "  ⚠  Afbeelding niet gevonden: {$filename}.png\n";
        $missing++;
        continue;
    }

    $productName = prettifyName($filename);

    $check = $pdo->prepare("SELECT id FROM products WHERE LOWER(name) = LOWER(?)");
    $check->execute([$productName]);
    if ($check->fetch()) {
        $skipped++;
        continue;
    }

    // Kopieer afbeelding naar storage
    copy($srcFile, $destDir . '/' . $filename . '.png');

    $barcode = 'AQF-' . strtoupper(substr($filename, 0, 6)) . '-' . str_pad($created + 1, 3, '0', STR_PAD_LEFT);

    $ins = $pdo->prepare("
        INSERT INTO products (name, stock, is_active, is_flood_tool, product_category_id, image, barcode, created_at, updated_at)
        VALUES (?, 10, 1, 0, ?, ?, ?, ?, ?)
    ");
    $ins->execute([$productName, $catIds[$categoryName], "products/{$filename}.png", $barcode, $now, $now]);

    echo "  ✓  $productName [$categoryName]\n";
    $created++;
}

echo "\n✅ $created producten aangemaakt, $skipped overgeslagen, $missing afbeeldingen ontbreken.\n";

// ── Storage symlink ───────────────────────────────────────────────────────────
$link   = __DIR__ . '/public/storage';
$target = __DIR__ . '/storage/app/public';

if (! file_exists($link) && ! is_link($link)) {
    symlink($target, $link);
    echo "✅ Storage symlink aangemaakt (public/storage -> storage/app/public)\n";
} else {
    echo "ℹ  Storage symlink bestaat al.\n";
}

echo "\nKlaar! Open aquafin.test/product in je browser.\n";
