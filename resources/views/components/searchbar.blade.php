<form action="{{ $route }}" method="GET" id="searchForm">
    <input
        type="text"
        name="q"
        id="searchInput"
        value="{{ $query}}"
        placeholder="{{ $placeholder }}"
        class="w-full px-4 py-2 rounded-full border border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400"
        autocomplete="off"
    >
</form>

@if(isset($query) && $query !== '')
    @forelse($results as $item)
        <div>{{ $item->name }}</div>
    @empty
        <p>Geen resultaten gevonden.</p>
    @endforelse
@endif

<script>
    const input = document.getElementById('searchInput');
    const form = document.getElementById('searchForm');
    let timer;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => {
            form.submit();
        }, 400);
    });
</script>