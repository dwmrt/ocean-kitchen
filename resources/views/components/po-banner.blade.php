@props(['po'])

@php
    $dibuka = $po['dibuka'] ?? true;
    $adaJadwal = ($po['buka'] ?? '-') !== '-';
@endphp

@if ($dibuka)
    <div class="bg-[#efdbce] border border-brand px-6 py-5">
        <b class="bg-white text-brand px-2 py-1 mr-3">■ PO DIBUKA</b>
        Periode Pre-Order: <b>{{ $po['buka'] }} - {{ $po['tutup'] }}</b>
        · Pesanan diterima {{ $po['diterima'] }}
    </div>
@elseif ($adaJadwal)
    <div class="bg-card border border-sand px-6 py-5">
        <b class="bg-sand text-ink px-2 py-1 mr-3">■ PO BELUM DIBUKA</b>
        Pre-order berikutnya dibuka <b>{{ $po['buka'] }}</b> sampai <b>{{ $po['tutup'] }}</b>
        · Pesanan diterima {{ $po['diterima'] }}
    </div>
@else
    <div class="bg-card border border-sand px-6 py-5">
        <b class="bg-sand text-ink px-2 py-1 mr-3">■ PO DITUTUP</b>
        Saat ini belum ada periode pre-order. Jadwal berikutnya akan diumumkan di sini.
    </div>
@endif