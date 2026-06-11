@if ($status === 'geleverd')
    <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 text-xs font-medium px-2.5 py-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Geleverd
    </span>
@elseif ($status === 'goedgekeurd')
    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        Goedgekeurd
    </span>
@elseif ($status === 'afgekeurd')
    <span class="inline-flex items-center gap-1 bg-red-100 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Afgekeurd
    </span>
@else
    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-700 text-xs font-medium px-2.5 py-1 rounded-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        In behandeling
    </span>
@endif
