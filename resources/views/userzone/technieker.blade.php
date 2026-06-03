<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/technieker.css') }}">
    <title>Dashboard</title>
</head>
<body>

<div class="technieker-container">

    <div class="page-header">
        <h1>Aquafin Dashboard</h1>

        <div>
            <button id="openCartBtn">
                Winkelwagen (2)
            </button>

            <button class="notification-btn">
                Meldingen (3)
            </button>
        </div>
    </div>

    <div class="search-section">
        <input
            type="text"
            placeholder="Zoek materiaal..."
            class="search-input"
        >
    </div>

    <div class="suggestions-section">
        <h2>Veelgebruikte materialen</h2>

        <div class="suggestions">
            <button class="suggestion-card">PVC Buis 50mm</button>
            <button class="suggestion-card">Koppeling 90°</button>
            <button class="suggestion-card">Afsluitkraan</button>
            <button class="suggestion-card">Watermeter</button>
        </div>
    </div>

    <div class="products-section">
        <h2>Beschikbaar materiaal</h2>

        <div class="product-list">

            <div class="product-card">
                <h3>PVC Buis 50mm</h3>
                <p>Voorraad: 120</p>
                <button>Toevoegen</button>
            </div>

            <div class="product-card">
                <h3>Koppeling 90°</h3>
                <p>Voorraad: 75</p>
                <button>Toevoegen</button>
            </div>

            <div class="product-card">
                <h3>Afsluitkraan</h3>
                <p>Voorraad: 42</p>
                <button>Toevoegen</button>
            </div>

        </div>
    </div>

    <div class="history-section">
        <button class="history-btn">
            Vorige bestellingen bekijken
        </button>
    </div>

</div>

<!-- Overlay -->
<div id="cartOverlay"></div>

<!-- Sliding Cart -->
<div id="cartPanel">

    <button id="closeCartBtn">✕</button>

    <h2>Bestelling</h2>

    <ul class="cart-items">
        <li>PVC Buis 50mm x2</li>
        <li>Koppeling 90° x4</li>
    </ul>

    <h3>Leverdatum</h3>

    <input type="date" class="date-picker">

    <button class="confirm-btn">
        Bestelling bevestigen
    </button>

</div>

<script>

    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');

    document.getElementById('openCartBtn')
        .addEventListener('click', () => {

            cartPanel.classList.add('open');
            cartOverlay.classList.add('open');

        });

    document.getElementById('closeCartBtn')
        .addEventListener('click', closeCart);

    cartOverlay.addEventListener('click', closeCart);

    function closeCart() {

        cartPanel.classList.remove('open');
        cartOverlay.classList.remove('open');

    }

</script>

</body>
</html>
