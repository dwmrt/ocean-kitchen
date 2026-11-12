@props(['po'])

<div class="bg-[#efdbce] border border-brand px-6 py-5">
    <b class="bg-white text-brand px-2 py-1 mr-3">■ PO DIBUKA</b>
    Periode Pre-Order: <b>{{ $po['buka'] }} - {{ $po['tutup'] }}</b>
    · Pesanan diterima {{ $po['diterima'] }}
</div>