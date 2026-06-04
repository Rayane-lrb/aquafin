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
                Productmand (<span id="cartCount">0</span>)
            </button>
            <button
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                Meldingen (3)
            </button>
        </div>
    </div>

    <form method="GET" action="{{ route('technieker') }}" class="mb-8 flex gap-4">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Zoek materiaal..."
            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <select
            name="category"
            onchange="this.form.submit()"
            class="border border-gray-300 rounded-lg px-4 py-3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Alle categorieën</option>
            <option value="1" {{ request('category') == 1 ? 'selected' : '' }}>Bevestigingsmateriaal</option>
            <option value="2" {{ request('category') == 2 ? 'selected' : '' }}>Persoonlijke beschermingsmiddelen (PBM)</option>
            <option value="3" {{ request('category') == 3 ? 'selected' : '' }}>Gereedschap (manueel & elektrisch)</option>
            <option value="4" {{ request('category') == 4 ? 'selected' : '' }}>Technische onderhoudsmaterialen</option>
            <option value="5" {{ request('category') == 5 ? 'selected' : '' }}>Specifieke Aquafin/riolering gerelateerde tools</option>
            <option value="6" {{ request('category') == 6 ? 'selected' : '' }}>Diversen / Verbruiksgoederen</option>
        </select>
        <button
            type="submit"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700 transition">
            Zoeken
        </button>
    </form>

    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">
            Beschikbaar materiaal
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
            <div class="bg-white p-5 rounded-xl shadow">
                <h3 class="font-semibold text-lg product-name">
                    {{ $product->name }}
                </h3>
                <p class="text-gray-600 mt-2">
                    Voorraad: {{ $product->stock }}
                </p>
                <div class="mt-4">
                    <button
                        onclick="showQuantitySelector(this)"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        Toevoegen
                    </button>
                    <div
                        class="quantity-selector hidden mt-3"
                        data-name="{{ $product->name }}"
                        data-id="{{ $product->id }}">
                        <input
                            type="number"
                            min="1"
                            max="{{ $product->stock }}"
                            value="1"
                            class="quantity-input border rounded px-3 py-2 w-24">
                        <button
                            onclick="confirmAdd(this)"
                            class="bg-blue-600 text-white px-3 py-2 rounded ml-2 hover:bg-blue-700">
                            Bevestigen
                        </button>
                        <button
                            onclick="cancelAdd(this)"
                            class="bg-red-600 text-white px-3 py-2 rounded ml-2 hover:bg-red-700">
                            Annuleren
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div>
        <button class="bg-gray-800 text-white px-5 py-3 rounded-lg hover:bg-gray-900 transition">
            Vorige bestellingen bekijken
        </button>
    </div>
</div>

<!-- Overlay -->
<div id="cartOverlay" class="fixed inset-0 bg-black/40 hidden z-40"></div>

<!-- Productmand -->
<div id="cartPanel" class="fixed top-0 right-0 w-96 h-screen bg-white shadow-2xl p-6 z-50 transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
    <button id="closeCartBtn" class="float-right text-2xl font-bold hover:text-gray-500">✕</button>
    <h2 class="text-2xl font-bold mb-6">Productmand</h2>
    <div id="cartItems" class="space-y-3 mb-6"></div>
    <h3 class="font-semibold mb-3">Leverdatum</h3>
    <input type="date" class="w-full border border-gray-300 rounded-lg p-2 mb-6">
    <button class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
        Bestelling bevestigen
    </button>
</div>

<script>
    // Elementen ophalen
    const cartPanel = document.getElementById('cartPanel');
    const cartOverlay = document.getElementById('cartOverlay');
    const openCartBtn = document.getElementById('openCartBtn');
    const closeCartBtn = document.getElementById('closeCartBtn');
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');

    // Productmand object
    let cart = {};

    // Productmand openen
    openCartBtn.addEventListener('click', () => {
        cartPanel.classList.remove('translate-x-full');
        cartOverlay.classList.remove('hidden');
    });

    // Productmand sluiten
    function closeCart() {
        cartPanel.classList.add('translate-x-full');
        cartOverlay.classList.add('hidden');
    }

    closeCartBtn.addEventListener('click', closeCart);
    cartOverlay.addEventListener('click', closeCart);

    // Toon hoeveelheidsselector
    function showQuantitySelector(button) {
        const productCard = button.closest('.bg-white');
        const quantitySelector = productCard.querySelector('.quantity-selector');
        quantitySelector.classList.remove('hidden');
        button.classList.add('hidden');
    }

    // Bevestig toevoegen
    function confirmAdd(button) {
        const quantitySelector = button.closest('.quantity-selector');
        const productName = quantitySelector.dataset.name;
        const productId = quantitySelector.dataset.id;
        const quantity = parseInt(quantitySelector.querySelector('.quantity-input').value);

        // Voeg toe aan mand
        if (cart[productId]) {
            cart[productId].quantity += quantity;
        } else {
            cart[productId] = {
                name: productName,
                quantity: quantity
            };
        }

        // Verberg selector en toon knop weer
        quantitySelector.classList.add('hidden');
        const addButton = quantitySelector.previousElementSibling;
        addButton.classList.remove('hidden');

        // Update mand
        updateCart();
    }

    // Annuleer toevoegen
    function cancelAdd(button) {
        const quantitySelector = button.closest('.quantity-selector');
        quantitySelector.classList.add('hidden');
        const addButton = quantitySelector.previousElementSibling;
        addButton.classList.remove('hidden');
    }

    // Update productmand
    function updateCart() {
        cartItems.innerHTML = '';
        let totalItems = 0;

        for (const productId in cart) {
            const product = cart[productId];
            totalItems += product.quantity;

            cartItems.innerHTML += `
                <div class="border rounded-lg p-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold">${product.name}</p>
                        <p class="text-sm text-gray-600">Aantal: ${product.quantity}</p>
                    </div>
                    <button
                        onclick="removeProduct('${productId}')"
                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                        Verwijder
                    </button>
                </div>
            `;
        }

        cartCount.textContent = totalItems;
    }

    // Verwijder product uit mand
    function removeProduct(productId) {
        delete cart[productId];
        updateCart();
    }
</script>

</body>
</html>
