<x-app-layout>
    <x-slot name="header">Bestellingen</x-slot>

    {{-- Toast container --}}
    <div id="aq-toast"
         class="fixed top-4 right-4 z-50 hidden max-w-sm text-sm font-medium px-4 py-3 rounded-xl shadow-lg">
    </div>

    {{-- ══ MELDINGEN voor technieker ═══════════════════════════════ --}}
    @if (Auth::user()->role === 'technieker')
    @php $unread = auth()->user()->unreadNotifications; @endphp
    @if ($unread->isNotEmpty())
    <div id="melding-container" class="space-y-2 mb-6">
        @foreach ($unread as $notif)
        @php $d = $notif->data; @endphp
        <div class="melding-item flex items-start gap-3 bg-white border-l-4 rounded-xl shadow-sm px-4 py-3
                    {{ $d['status'] === 'goedgekeurd' ? 'border-green-500' : ($d['status'] === 'geleverd' ? 'border-blue-500' : 'border-red-400') }}"
             data-id="{{ $notif->id }}">
            <span class="text-xl shrink-0 mt-0.5">{{ $d['icon'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800">{{ $d['message'] }}</p>
                <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            <button onclick="sluitMelding(this)" class="text-gray-300 hover:text-gray-500 transition shrink-0 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endforeach
    </div>
    <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function sluitMelding(btn) {
        const item = btn.closest('.melding-item');
        item.style.transition = 'opacity .3s, transform .3s';
        item.style.opacity = '0';
        item.style.transform = 'translateX(20px)';
        setTimeout(() => item.remove(), 300);

        // Als alle meldingen gesloten → markeer als gelezen
        const container = document.getElementById('melding-container');
        if (!container.querySelector('.melding-item:not([style*="opacity: 0"])')) {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
            });
        }
    }

    // Auto-markeer als gelezen na 8 seconden
    setTimeout(() => {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
    }, 8000);
    </script>
    @endif
    @endif

    {{-- Header acties --}}
    <div class="flex justify-between items-center mb-6">
        <p class="text-sm text-gray-500">
            @if(Auth::user()->role === 'technieker')
                Jouw bestellingen en leveringsstatus
            @else
                Alle bestellingen beheren en afleveren
            @endif
        </p>
        <a href="{{ route('order.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            + Nieuwe bestelling
        </a>
    </div>

    {{-- Status filter tabs (client-side, geen reload) --}}
    @php
        $tabs = [
            'alle'           => ['label' => 'Alle',           'count' => $orders->count()],
            'in behandeling' => ['label' => 'In behandeling', 'count' => $orders->where('status','in behandeling')->count()],
            'goedgekeurd'    => ['label' => 'Goedgekeurd',    'count' => $orders->where('status','goedgekeurd')->count()],
            'geleverd'       => ['label' => 'Geleverd',       'count' => $orders->where('status','geleverd')->count()],
            'afgekeurd'      => ['label' => 'Afgekeurd',      'count' => $orders->where('status','afgekeurd')->count()],
        ];
    @endphp

    <div class="flex gap-1 mb-6 bg-gray-100 p-1 rounded-xl overflow-x-auto w-full sm:w-fit" id="tab-bar">
        @foreach ($tabs as $key => $t)
            <button type="button" data-tab="{{ $key }}"
                    class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium transition whitespace-nowrap
                           {{ $key === 'alle' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $t['label'] }}
                @if ($t['count'] > 0)
                    <span class="ml-1 tab-count {{ $key === 'alle' ? 'text-blue-600' : 'text-gray-400' }}">{{ $t['count'] }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Orders gegroepeerd per gebruiker --}}
    <div id="groups-container">
    @forelse ($grouped as $userId => $userOrders)
        @php
            $hasUrgent  = $userOrders->contains(fn($o) => $o->urgent);
            $firstOrder = $userOrders->first();
        @endphp

        <div class="user-group mb-6"
             data-user-id="{{ $userId }}"
             data-has-urgent="{{ $hasUrgent ? '1' : '0' }}">

            {{-- Groep header --}}
            <div class="group-header flex items-center gap-3 mb-2 px-1">
                <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr($firstOrder->user->name ?? '?', 0, 1)) }}
                </div>
                <span class="font-semibold text-gray-700 text-sm">{{ $firstOrder->user->name ?? '—' }}</span>
                <span class="text-xs text-gray-400">({{ $userOrders->count() }} bestelling{{ $userOrders->count() !== 1 ? 'en' : '' }})</span>
                @if ($hasUrgent)
                    <span class="group-urgent-badge ml-1 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">🚨 DRINGEND</span>
                @else
                    <span class="group-urgent-badge hidden ml-1 bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full animate-pulse">🚨 DRINGEND</span>
                @endif
            </div>

            {{-- Tabel --}}
            <div class="bg-white shadow-sm rounded-xl overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-3">Product</th>
                            @if(Auth::user()->role !== 'technieker')
                                <th class="px-6 py-3">Datum</th>
                            @endif
                            <th class="px-6 py-3">Aantal</th>
                            <th class="px-6 py-3">Magazijn</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Acties</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 order-tbody">
                        @foreach ($userOrders as $order)
                        <tr class="order-row hover:bg-gray-50 transition {{ ($order->urgent ?? false) ? 'bg-red-50 border-l-4 border-red-500' : '' }}"
                            data-order-id="{{ $order->id }}"
                            data-status="{{ $order->status }}"
                            data-urgent="{{ ($order->urgent ?? false) ? '1' : '0' }}"
                            data-owner-id="{{ $order->user_id }}">

                            {{-- Product + urgent badge --}}
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $order->product?->name ?? '—' }}
                                <span class="urgent-badge {{ ($order->urgent ?? false) ? '' : 'hidden' }} ml-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded">🚨 DRINGEND</span>
                            </td>

                            @if(Auth::user()->role !== 'technieker')
                                <td class="px-6 py-4">
                                    <div class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                            @endif

                            <td class="px-6 py-4 text-gray-700">{{ $order->quantity }}</td>

                            <td class="px-6 py-4">
                                @if ($order->warehouse)
                                    <div class="text-gray-800 font-medium leading-tight">{{ $order->warehouse->name }}</div>
                                    @if ($order->warehouse->address)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $order->warehouse->address }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 status-cell">
                                @include('order._status_badge', ['status' => $order->status])
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex gap-2 flex-wrap items-center actions-cell">

                                    @if ($order->status === 'in behandeling')
                                        @if ($order->user_id === Auth::id() || Auth::user()?->role === 'admin')
                                            <a href="{{ route('order.edit', $order->id) }}"
                                               class="text-xs font-medium bg-gray-50 text-gray-600 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition border border-gray-200">
                                                Bewerken
                                            </a>
                                        @endif
                                        @if (in_array(Auth::user()?->role, ['magazijnBeheerder', 'admin']))
                                            <form action="{{ route('order.approve', $order->id) }}" method="POST" data-ajax data-action="approve">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-medium bg-green-50 text-green-600 hover:bg-green-100 px-3 py-1.5 rounded-lg transition border border-green-200">
                                                    Goedkeuren
                                                </button>
                                            </form>
                                            <form action="{{ route('order.reject', $order->id) }}" method="POST" data-ajax data-action="reject">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-medium bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition border border-red-200">
                                                    Weigeren
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                    @if ($order->status === 'goedgekeurd' && in_array(Auth::user()?->role, ['magazijnBeheerder', 'admin']))
                                        <form action="{{ route('order.deliver', $order->id) }}" method="POST" data-ajax data-action="deliver">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs font-medium bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                                Lever af
                                            </button>
                                        </form>
                                    @endif

                                    @if ($order->user_id === Auth::id() && !in_array($order->status, ['geleverd', 'afgekeurd']))
                                        <form action="{{ route('order.urgent', $order->id) }}" method="POST" data-ajax data-action="urgent">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="urgent-toggle-btn text-xs font-medium px-3 py-1.5 rounded-lg transition border
                                                        {{ ($order->urgent ?? false) ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' : 'bg-white text-red-500 border-red-200 hover:bg-red-50' }}">
                                                {{ ($order->urgent ?? false) ? '🚨 Opheffen' : '🚨 DRINGEND' }}
                                            </button>
                                        </form>
                                    @endif

                                    @if (!in_array($order->status, ['in behandeling', 'goedgekeurd']) && $order->user_id !== Auth::id())
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm px-6 py-14 text-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Geen bestellingen gevonden.
        </div>
    @endforelse
    </div>

    {{-- Legende technieker --}}
    @if(Auth::user()->role === 'technieker')
        <div class="mt-6 bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-700">
            <p class="font-medium mb-2">Wat betekent de status?</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center gap-2">
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">In behandeling</span>
                    <span class="text-blue-600">Wachten op goedkeuring</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Goedgekeurd</span>
                    <span class="text-blue-600">Wordt voorbereid</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">Geleverd</span>
                    <span class="text-blue-600">Klaar op het magazijn</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded-full">Afgekeurd</span>
                    <span class="text-blue-600">Niet verwerkt</span>
                </div>
            </div>
        </div>
    @endif

<script>
/* ===================================================
   Tab filter — client-side, geen page reload
   =================================================== */
const tabBar = document.getElementById('tab-bar');
let activeTab = 'alle';

tabBar.addEventListener('click', e => {
    const btn = e.target.closest('.tab-btn');
    if (!btn) return;
    activeTab = btn.dataset.tab;

    tabBar.querySelectorAll('.tab-btn').forEach(b => {
        const on = b === btn;
        b.classList.toggle('bg-white', on);
        b.classList.toggle('shadow', on);
        b.classList.toggle('text-gray-800', on);
        b.classList.toggle('text-gray-500', !on);
        const cnt = b.querySelector('.tab-count');
        if (cnt) {
            cnt.classList.toggle('text-blue-600', on);
            cnt.classList.toggle('text-gray-400', !on);
        }
    });

    applyTabFilter();
});

function applyTabFilter() {
    document.querySelectorAll('tr.order-row').forEach(row => {
        row.style.display = (activeTab === 'alle' || row.dataset.status === activeTab) ? '' : 'none';
    });
    document.querySelectorAll('.user-group').forEach(group => {
        const anyVisible = [...group.querySelectorAll('tr.order-row')].some(r => r.style.display !== 'none');
        group.style.display = anyVisible ? '' : 'none';
    });
}

/* ===================================================
   AJAX — onderschep alle forms met data-ajax
   =================================================== */
document.addEventListener('submit', e => {
    const form = e.target.closest('form[data-ajax]');
    if (!form) return;
    e.preventDefault();

    const btn = form.querySelector('button[type=submit]');
    if (btn) btn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const row = form.closest('tr.order-row');
            form.dataset.action === 'urgent'
                ? handleUrgentToggle(row, data)
                : handleStatusChange(row, data.status);
            showToast(data.message || 'Opgeslagen ✓', 'success');
        } else {
            showToast(data.message || 'Er is een fout opgetreden.', 'error');
            if (btn) btn.disabled = false;
        }
    })
    .catch(() => {
        showToast('Verbindingsfout. Probeer opnieuw.', 'error');
        if (btn) btn.disabled = false;
    });
});

/* ===================================================
   Status badge HTML map
   =================================================== */
const statusBadges = {
    'geleverd':      `<span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Geleverd</span>`,
    'goedgekeurd':   `<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Goedgekeurd</span>`,
    'afgekeurd':     `<span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>Afgekeurd</span>`,
    'in behandeling':`<span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-medium px-2.5 py-1 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>In behandeling</span>`,
};

/* ===================================================
   Status verandering (approve / reject / deliver)
   =================================================== */
function handleStatusChange(row, newStatus) {
    row.dataset.status = newStatus;
    row.querySelector('.status-cell').innerHTML = statusBadges[newStatus] || newStatus;

    const actionsCell = row.querySelector('.actions-cell');
    if (['geleverd', 'afgekeurd'].includes(newStatus)) {
        actionsCell.innerHTML = '<span class="text-gray-300 text-xs">—</span>';
    } else if (newStatus === 'goedgekeurd') {
        // Verwijder enkel goedkeuren/weigeren knoppen
        actionsCell.querySelectorAll('form[data-action="approve"], form[data-action="reject"]').forEach(f => f.remove());
    }

    applyTabFilter();
}

/* ===================================================
   Urgentie toggle
   =================================================== */
function handleUrgentToggle(row, data) {
    const isUrgent = data.urgent;
    row.dataset.urgent = isUrgent ? '1' : '0';

    // Row styling
    row.classList.toggle('bg-red-50', isUrgent);
    row.classList.toggle('border-l-4', isUrgent);
    row.classList.toggle('border-red-500', isUrgent);

    // Inline badge
    const badge = row.querySelector('.urgent-badge');
    if (badge) badge.classList.toggle('hidden', !isUrgent);

    // Knop tekst + stijl
    const btn = row.querySelector('.urgent-toggle-btn');
    if (btn) {
        btn.textContent = isUrgent ? '🚨 Opheffen' : '🚨 DRINGEND';
        btn.className = 'urgent-toggle-btn text-xs font-medium px-3 py-1.5 rounded-lg transition border '
            + (isUrgent
                ? 'bg-red-600 text-white border-red-600 hover:bg-red-700'
                : 'bg-white text-red-500 border-red-200 hover:bg-red-50');
        btn.disabled = false;
    }

    // Herorden rijen binnen groep: urgent bovenaan
    const tbody = row.closest('tbody.order-tbody');
    if (tbody) {
        [...tbody.querySelectorAll('tr.order-row')]
            .sort((a, b) => (b.dataset.urgent === '1' ? 1 : 0) - (a.dataset.urgent === '1' ? 1 : 0))
            .forEach(r => tbody.appendChild(r));
    }

    // Update groep header badge
    const group = row.closest('.user-group');
    if (group) {
        const groupHasUrgent = [...group.querySelectorAll('tr.order-row')].some(r => r.dataset.urgent === '1');
        group.dataset.hasUrgent = groupHasUrgent ? '1' : '0';

        const urgBadge = group.querySelector('.group-urgent-badge');
        if (urgBadge) urgBadge.classList.toggle('hidden', !groupHasUrgent);

        // Herorden groepen: urgent-groepen bovenaan
        const container = document.getElementById('groups-container');
        [...container.querySelectorAll('.user-group')]
            .sort((a, b) => (b.dataset.hasUrgent === '1' ? 1 : 0) - (a.dataset.hasUrgent === '1' ? 1 : 0))
            .forEach(g => container.appendChild(g));
    }
}

/* ===================================================
   Toast notificaties
   =================================================== */
function showToast(msg, type) {
    const toast = document.getElementById('aq-toast');
    toast.textContent = msg;
    toast.className = 'fixed top-4 right-4 z-50 max-w-sm text-sm font-medium px-4 py-3 rounded-xl shadow-lg '
        + (type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-600');
    toast.style.opacity = '1';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => toast.classList.add('hidden'), 350);
    }, 3000);
}
</script>

</x-app-layout>
