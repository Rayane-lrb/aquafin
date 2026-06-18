<?php
/**
 * sync-server.php — synchronise le serveur avec le local
 * 1. Supprime les anciennes catégories + produits
 * 2. Crée les nouvelles catégories + produits
 * 3. Lie les images aux produits
 *
 * Run: php sync-server.php
 */

// ── Connexion DB (Laravel .env) ───────────────────────────────────
$env = [];
foreach (file(__DIR__ . '/.env') as $line) {
    $line = trim($line);
    if (!$line || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
}

$driver = $env['DB_CONNECTION'] ?? 'mysql';

if ($driver === 'sqlite') {
    $db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
} else {
    $host = $env['DB_HOST'] ?? '127.0.0.1';
    $port = $env['DB_PORT'] ?? '3306';
    $name = $env['DB_DATABASE'] ?? '';
    $user = $env['DB_USERNAME'] ?? '';
    $pass = $env['DB_PASSWORD'] ?? '';
    $db   = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass);
}

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "✓ DB verbonden ({$driver})\n\n";

// ── Stap 1: Oude categorieën verwijderen ──────────────────────────
$oldCats = [
    'Bouten & Schroeven', 'Moeren & Ringen', 'Draadstangen',
    'Leidingen & Koppelingen', 'Gereedschap & Machines',
    'Inspectie & Meting', 'Verbruiksmaterialen', 'Elektrisch', 'Aandrijving',
    'Afdichtings- & Smeermiddelen', 'Pompen & Leidingen',
    'Meetapparatuur', 'Gereedschap & Diversen',
];

echo "🗑  Oude categorieën verwijderen...\n";
foreach ($oldCats as $name) {
    $cat = $db->prepare("SELECT id FROM product_categories WHERE name = ?");
    $cat->execute([$name]);
    $row = $cat->fetch(PDO::FETCH_ASSOC);
    if (!$row) continue;

    $db->prepare("DELETE FROM products WHERE product_category_id = ?")->execute([$row['id']]);
    $db->prepare("DELETE FROM product_categories WHERE id = ?")->execute([$row['id']]);
    echo "   ✓ {$name}\n";
}

// ── Stap 2: Nieuwe catalogus aanmaken ────────────────────────────
$catalog = [
    'Bevestigingsmateriaal' => [
        'Bout M6 inox A2','Bout M8 inox A2','Bout M10 inox A2','Bout M12 inox A2','Bout M16 inox A2',
        'Bout M6 inox A4','Bout M8 inox A4','Bout M10 inox A4','Bout M12 inox A4','Bout M16 inox A4',
        'Bout M6 verzinkt','Bout M8 verzinkt','Bout M10 verzinkt','Bout M12 verzinkt','Bout M16 verzinkt',
        'Zeskantmoer M6','Zeskantmoer M8','Zeskantmoer M10','Zeskantmoer M12','Zeskantmoer M16',
        'Borgmoer M8','Borgmoer M10','Borgmoer M12',
        'Flensmoer M8','Flensmoer M10','Flensmoer M12',
        'Sluitring M6','Sluitring M8','Sluitring M10','Sluitring M12','Sluitring M16',
        'Veerring M8','Veerring M10','Veerring M12','Tandring M8','Tandring M10',
        'Ankerbout M8','Ankerbout M10','Ankerbout M12',
        'Chemisch anker Hilti HIT M10','Chemisch anker Hilti HIT M12',
        'Keilbout M8','Keilbout M10','Keilbout M12',
        'Draadstang M6','Draadstang M8','Draadstang M10','Draadstang M12','Draadstang M16',
        'Inslagmoer M6','Inslagmoer M8','Inslagmoer M10','Tapbout M8','Tapbout M10',
        'Zeskantkopbout M8','Zeskantkopbout M10','Zeskantkopbout M12',
        'Inbusbout M6','Inbusbout M8','Inbusbout M10',
        'Torx-schroef T20','Torx-schroef T25','Torx-schroef T30',
        'Kruiskopschroef 4x40','Kruiskopschroef 4x60','Kruiskopschroef 5x80',
        'Zelftappende vijs 4,2x13','Zelftappende vijs 4,8x19',
        'Parkervijs 4x16','Parkervijs 5x25','Spaanplaatschroef 4x40','Spaanplaatschroef 5x60',
        'Slangklem ø20-32','Slangklem ø32-50','Slangklem ø50-70','Slangklem ø70-90',
    ],
    'Persoonlijke beschermingsmiddelen' => [
        'Veiligheidshelm met kinband','Oordoppen (doos 200 stuks)','Gehoorkappen',
        'Veiligheidsbril','Gelaatsscherm','Stofmasker FFP2','Stofmasker FFP3',
        'Werkhandschoenen snijvast','Werkhandschoenen chemisch resistent','Werkhandschoenen elektrisch geïsoleerd',
        'Veiligheidsschoenen S3','Veiligheidsschoenen antistatisch',
        'Werklaarzen PVC','Werklaarzen nitril met stalen zool',
        'Regenjas','Regenbroek','Regencape','Fluovest EN ISO 20471','Signalisatiekledij klasse 2',
        'Overall brandvertragend','Overall antistatisch','Overall waterafstotend',
        'Valharnas','Lifeline 10m','Gasdetectiemeter O₂/CH₄/H₂S/CO',
        'EHBO-kit standaard','Handontsmetting 500ml','Karabijnhaak','Klimgordel',
    ],
    'Gereedschap' => [
        'Dopsleutelset metrisch 1/2"','Dopsleutelset inch 1/2"','Dopsleutelset metrisch 3/8"',
        'Ringsleutel 10-11','Ringsleutel 13-15','Ringsleutel 17-19','Ringsleutel 22-24',
        'Steeksleutel 13','Steeksleutel 17','Steeksleutel 19',
        'Momentsleutel 20-110 Nm','Momentsleutel 40-210 Nm',
        'Inbussleutelset metrisch','Inbussleutelset Torx',
        'Schroevendraaier plat 5mm','Schroevendraaier plat 8mm',
        'Schroevendraaier kruiskop PH2','Schroevendraaier kruiskop PH3',
        'Schroevendraaier Torx T25','Schroevendraaier geïsoleerd set',
        'Combinatietang 200mm','Waterpomptang 300mm','Kniptang 180mm','Punttang 160mm',
        'Krimptang adereindhulzen','Kabelschoentang','Kabelstripper 0,5-6mm²',
        'Hamer 500g','Kunststofhamer 40mm','Moker 2kg','Breekijzer 500mm','Breekijzer 800mm',
        'Haakse slijper 115mm','Haakse slijper 125mm','Accuboormachine 18V',
        'Klopboormachine SDS+','Schroefmachine 18V',
        'Slagmoersleutel pneumatisch 1/2"','Slagmoersleutel accu 18V',
        'Waterpas 60cm','Laserwaterpas','Meetlint 5m','Rolmeter 25m',
        'Spanningstester','Multimeter digitaal',
    ],
    'Technische onderhoudsmaterialen' => [
        'Smeervet EP2 lithium 400g','Smeervet foodgrade 400g',
        'O-ring ø10 EPDM','O-ring ø20 EPDM','O-ring ø30 EPDM','O-ring ø50 EPDM',
        'O-ring ø10 NBR','O-ring ø20 NBR','O-ring ø30 NBR',
        'Pakking papier 1mm','Pakking rubber 2mm','Pakking EPDM 3mm',
        'PTFE tape 12mm','Loctite 243 (middel-sterk)','Loctite 270 (sterk)',
        'PVC-slang ø25mm/m','PE-slang ø32mm/m','Persslang ø40mm/m',
        'PVC T-stuk ø40','PVC T-stuk ø50','PVC T-stuk ø63',
        'PVC bocht 90° ø40','PVC bocht 90° ø50','PVC bocht 45° ø63',
        'Geka-koppeling ø25','Geka-koppeling ø40','Gardena-koppeling ø25',
        'Camlock koppeling type A ø50','Camlock koppeling type C ø75',
        'V-snaar A-sectie','V-snaar B-sectie','Ketting 1/2" per m',
        'Kabel + wartel M16','Kabel + wartel M20','Kabel + wartel M25','Kabel + wartel M32',
        'Aansluitdoos IP55','Aansluitdoos IP65','Drukleidingsysteem ø40','Afvoerleidingsysteem ø50',
        'Pneumatische koppeling 1/4"','Pneumatische koppeling 3/8"',
        'Trillingsdemper M8','Trillingsdemper M10',
    ],
    'Specifieke riolering & Aquafin tools' => [
        'Putdekselhaak','Mangatopener','Rioolcamera DN100','Inspectiecamera ø63-150mm',
        'Gasdetectietoestel H₂S','Gasdetectietoestel CO','Gasdetectietoestel O₂ + CH₄',
        'Ontstoppingsveer 10m','Ontstoppingsveer 20m',
        'Hogedrukreiniger 150 bar','Hogedrukreiniger 200 bar',
        'Slangenwagen 40m','Slangenwagen 60m',
        'Dompelpomp 230V','Dompelpomp 400V','Dompelpomp ATEX',
        'Rioolstop ø150','Rioolstop ø200','Rioolstop ø300',
        'Vlotterschakelaar','Niveauschakelaar','Ultrasone niveaumeter','Radarniveaumeter',
        'Staalnamepot 1L','Staalnamepot 2,5L','Monsternameapparatuur automatisch',
    ],
    'Diversen & Verbruiksgoederen' => [
        'Tie-wraps 200mm (zak 100)','Tie-wraps 300mm (zak 100)','Tie-wraps 400mm (zak 50)',
        'Kabelschoen 0,5-1,5mm² (doos)','Kabelschoen 1,5-2,5mm² (doos)','Kabelschoen 4-6mm² (doos)',
        'Markeringstape geel/zwart 50mm','Markeringstape rood/wit 50mm',
        'Siliconenkit transparant','Siliconenkit wit','Reparatielijm epoxy',
        'Reinigingsdoekjes (rags) x100','WD-40 400ml','Contactspray 400ml','Kettingspray 400ml',
        'Duct tape 50mm grijs','Duct tape 50mm zwart',
        'Isolatietape zwart 15mm','Isolatietape rood 15mm',
        'AA batterijen (pak 10)','AA batterijen (pak 20)','D-cel batterijen (pak 4)',
        'Reserverelais 24V','Reserverelais 230V','Fles perslucht 400ml',
    ],
];

echo "\n📦 Nieuwe catalogus aanmaken...\n";
$created = 0;
$skipped = 0;
$n = 1;

$getCat = $db->prepare("SELECT id FROM product_categories WHERE name = ?");
$insCat = $db->prepare("INSERT INTO product_categories (name, created_at, updated_at) VALUES (?, NOW(), NOW())");
$exists  = $db->prepare("SELECT id FROM products WHERE LOWER(name) = LOWER(?)");
$insProd = $db->prepare("INSERT INTO products (name, stock, is_active, is_flood_tool, product_category_id, barcode, created_at, updated_at) VALUES (?, 0, 1, 0, ?, ?, NOW(), NOW())");

foreach ($catalog as $catName => $products) {
    $getCat->execute([$catName]);
    $cat = $getCat->fetch(PDO::FETCH_ASSOC);
    if (!$cat) {
        $insCat->execute([$catName]);
        $catId = $db->lastInsertId();
    } else {
        $catId = $cat['id'];
    }

    foreach ($products as $productName) {
        $exists->execute([$productName]);
        if ($exists->fetch()) { $skipped++; continue; }

        $barcode = 'AQF-' . str_pad($n, 6, '0', STR_PAD_LEFT);
        $insProd->execute([$productName, $catId, $barcode]);
        $created++; $n++;
    }

    echo "   ✓ {$catName} (" . count($products) . " producten)\n";
}

echo "\n✅ {$created} aangemaakt, {$skipped} overgeslagen\n";

// ── Stap 3: Images koppelen ───────────────────────────────────────
echo "\n🖼  Afbeeldingen koppelen...\n";
$linked = 0;
$all = $db->query("SELECT id, name FROM products")->fetchAll(PDO::FETCH_ASSOC);
$upd = $db->prepare("UPDATE products SET image = ? WHERE id = ?");

foreach ($all as $p) {
    $fname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $p['name']) . '.png';
    $path  = __DIR__ . '/storage/app/public/products/' . $fname;
    if (file_exists($path)) {
        $upd->execute(['products/' . $fname, $p['id']]);
        $linked++;
    }
}

echo "✅ {$linked} producten gekoppeld aan afbeelding\n";
echo "\n🎉 Klaar! Server is nu gesynchroniseerd.\n";
echo "   Verwijder dit script: rm sync-server.php\n";
