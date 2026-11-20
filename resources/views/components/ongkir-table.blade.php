@props(['ongkir'])

<div class="overflow-x-auto">
    <table class="w-full border border-brand bg-card text-left">
        <thead class="bg-brand text-white">
            <tr>
                <th class="px-4 py-3">Jarak Pengiriman</th>
                <th class="px-4 py-3">Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ongkir as $o)
                <tr class="border-t border-sand">
                    <td class="px-4 py-3">{{ $o['wilayah'] }}</td>
                    <td class="px-4 py-3 font-semibold">
                        Rp {{ number_format($o['biaya'], 0, ',', '.') }}
                        @if (!empty($o['satuan']))
                            <span class="text-muted font-normal">{{ $o['satuan'] }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="border-t border-sand">
                    <td colspan="2" class="px-4 py-6 text-center text-muted">Tarif ongkir belum tersedia.</td>
                </tr>
            @endforelse
            <tr class="border-t border-sand">
                <td class="px-4 py-3">Ambil di toko</td>
                <td class="px-4 py-3 font-semibold">Gratis</td>
            </tr>
        </tbody>
    </table>
</div>