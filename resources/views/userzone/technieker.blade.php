<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquafin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="max-w-7xl mx-auto p-6">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Aquafin Dashboard
        </h1>

        <div class="flex gap-3">
            <button
                id="openCartBtn"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                Winkelwagen (2)
            </button>

            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Meldingen (3)
            </button>
        </div>
    </div>

    <div class="mb-8">
        <input
            type="text"
            placeholder="Zoek materiaal..."
            class="w-full border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">
            Veelgebruikte materialen
        </h2>

    </div>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">
            Beschikbaar materiaal
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)

            <div class="bg-white p-5 rounded-xl shadow">

                <h3 class="font-semibold text-lg">
                    {{ $product->name }}
                </h3>
                <p class="text-gray-600 mt-2">
                    Voorraad: {{ $product->stock }}
                </p>

                <button
                    class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Toevoegen
                </button>

            </div>

            @endforeach
        </div>
    </div>

    <div>
        <button
            class="bg-gray-800 text-white px-5 py-3 rounded-lg hover:bg-gray-900 transition">
            Vorige bestellingen bekijken
        </button>
    </div>
</div>

<div id="cartOverlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

<!-- Winkelwagen Panel (initieel buiten scherm) -->
<div id="cartPanel" class="fixed top-0 right-0 w-96 h-screen bg-white shadow-2xl p-6 z-50
    transform translate-x-full transition-transform duration-300 ease-in-out">
    <button id="closeCartBtn" class="float-right text-2xl font-bold hover:text-gray-500">
        ✕
    </button>
    <h2 class="text-2xl font-bold mb-6">
        Bestelling
    </h2>
    <ul class="space-y-2 mb-6">
        <li class="border-b pb-2">
            PVC Buis 50mm x2
        </li>
        <li class="border-b pb-2">
            Koppeling 90° x4
        </li>
    </ul>
    <h3 class="font-semibold mb-3">
        Leverdatum
    </h3>
    <input type="date" class="w-full border border-gray-300 rounded-lg p-2 mb-6">
    <button class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
        Bestelling bevestigen
    </button>
</div>

<script>
    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');
    const openCartBtn = document.getElementById('openCartBtn');
    const closeCartBtn = document.getElementById('closeCartBtn');

    // Open winkelwagen
    openCartBtn.addEventListener('click', () => {
        cartPanel.classList.remove('translate-x-full');
        cartOverlay.classList.remove('hidden');
    });

    // Sluit winkelwagen
    function closeCart() {
        cartPanel.classList.add('translate-x-full');
        cartOverlay.classList.add('hidden');
    }

    closeCartBtn.addEventListener('click', closeCart);
    cartOverlay.addEventListener('click', closeCart);
</script>

</body>
</html>
