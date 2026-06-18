
# Aquafin

**Intern magazijnbestelsysteem voor Aquafin-technici.**

Een rolgebaseerde voorraad- en bestelbeheerapplicatie gebouwd met Laravel 13, Alpine.js en Tailwind CSS. Technici kunnen producten bekijken, toevoegen aan een winkelwagen en bestellingen indienen. Magazijnbeheerders keuren bestellingen goed, wijzen ze af of leveren ze. Beheerders beheren gebruikers, magazijnen en het volledige proces.

## Functies

- **Rolgebaseerde toegang** — Drie rollen: `admin`, `magazijnBeheerder`, `technieker`. Elke rol ziet een op maat gemaakte interface.
- **Productencatalogus** — Zoeken, filteren op categorie, favorieten togglen (AJAX), voorraadniveaus bekijken. Verborgen producten worden met een visuele indicator getoond aan bevoorrechte rollen.
- **Winkelwagen** — Producten toevoegen/verwijderen/aantallen aanpassen zonder pagina-herladen (AJAX). Afrekenen creëert gegroepeerde bestellingen.
- **Bestelworkflow** — 4-stappen levenscyclus: `in behandeling` → `goedgekeurd` → `geleverd` (of `afgekeurd`). Technici kunnen bestellingen als dringend markeren. Magazijnbeheerders kunnen groepen in één keer goedkeuren of afwijzen.
- **Magazijndashboard** — Overzicht met teller voor dringende bestellingen, secties voor in behandeling/goedgekeurd/archief, groepsacties en picklijsten met streepjescodes.
- **Neerslagpagina** (`/neerslag`) — Haalt een 7-daagse weersvoorspelling op van Open-Meteo. Toont regenmeters, wekelijkse statistieken, een staafdiagram en beveelt producten aan op basis van regenintensiteit.
- **Productvoorstellen** — Technici kunnen nieuwe producten voorstellen; status bijgehouden als `in behandeling` / `goedgekeurd` / `afgekeurd`.
- **Meldingen** — In-app databasemeldingen bij goedkeuring, afwijzing of levering van bestellingen.
- **Donkere modus** — Volgt systeemvoorkeur, handmatige schakelaar in de bovenbalk en op de profielpagina, opgeslagen in localStorage.
- **Sorteerbare tabellen** — Klik op elke `<th>` om kolommen aan de clientzijde te sorteren.
- **Mobielvriendelijk** — Inklapbare zijbalk met overlay, aanpasbare kaartroosters.


## Open-Meteo
We gebruiken de gratis API van [Open-Meteo](https://open-meteo.com/) om gegevens op te halen over de neerslag

## Rollen

| Rol | Machtigingen |
|------|-------------|
| `technieker` | Catalogus doorbladeren, winkelwagen, bestellingen plaatsen, eigen bestellingen bekijken, dringend markeren, favorieten beheren, neerslagpagina bekijken, voorstellen indienen |
| `magazijnBeheerder` | Alle bestellingen dashboard, bestellingen goedkeuren/afwijzen/leveren, producten & categorieën beheren |
| `admin` | Alles hierboven + gebruikersbeheer (CRUD gebruikers, rollen toewijzen) en magazijnbeheer (CRUD magazijnen) |

## Technologische Stack

| Laag | Technologie |
|-------|------------|
| **Backend** | Laravel 13 (PHP 8.4) |
| **Frontend** | Blade-sjablonen, Alpine.js 3, Tailwind CSS 4 (CDN) |
| **Authenticatie** | Laravel Breeze (Blade + Alpine) |
| **Bundelen** | Vite |
| **Database** | SQLite (ontwikkeling), MySQL-compatibel schema |
| **Weer-API** | Open-Meteo (gratis, geen sleutel) |
| **Meldingen** | In-app databasemeldingen |
| **Testen** | Pest 4 |

## Aan de Slag

### Vereisten

- PHP 8.4+
- Composer
- Node.js & npm
- [Laravel Herd](https://herd.laravel.com/) (of je voorkeurs lokale server)

### Installatie

```bash
git clone <repository-url> aquafin
cd aquafin

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate
php artisan db:seed

npm run build
```

### Ontwikkeling

```bash
# Serveren via Herd (automatisch) of:
php artisan serve

# Frontend-assets compileren:
npm run dev

# Of gebruik de Vite-ontwikkelserver:
composer run dev
```

De site is beschikbaar op `http://aquafin.test` (Herd) of het adres van `php artisan serve`.

### Tests Uitvoeren

```bash
php artisan test --compact
```

## Database

| Tabel | Beschrijving |
|-------|-------------|
| `users` | Gebruikers met `role` enum (`admin`, `magazijnBeheerder`, `technieker`) |
| `products` | Catalogusitems met voorraad, streepjescode, actieve status, overstromings-/regenvlaggen |
| `product_categories` | Categoriegroepen voor producten |
| `orders` | Bestelregels met status, hoeveelheid, magazijn, groeps-ID, dringend-vlag |
| `warehouses` | Magazijnlocaties |
| `favorites` | Tussenliggende tabel voor gebruikersproduct-favorieten |
| `suggestions` | Productvoorstellen van technici |
| `notifications` | Laravel databasemeldingen |

## Projectstructuur

```
aquafin/
├── app/
│   ├── Http/
│   │   └── Controllers/    # CartController, OrderController, ProductController, etc.
│   ├── Models/             # User, Product, Order, ProductCategory, Warehouse, etc.
│   └── ...
├── resources/
│   └── views/
│       ├── layouts/        # app.blade.php, guest.blade.php, app_navigation.blade.php
│       ├── cart/           # Winkelwagenweergaven
│       ├── order/          # Bestellingenoverzicht, magazijndashboard
│       ├── product/        # Productencatalogus, tonen, formulier
│       ├── productcategory/ # Categorie-overzicht
│       ├── neerslag/       # Neerslag-/weerpagina
│       ├── profile/        # Gebruikersprofiel (donkere modus, accountinstellingen)
│       └── ...
├── routes/
│   └── web.php             # Alle webroutes
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
└── ...
```
