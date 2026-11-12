<footer id="ongkir" class="bg-dark text-white px-[5%] py-6 flex flex-wrap items-center gap-5 mt-10">
    @unless (request()->routeIs('katalog'))
        <b>Informasi ongkos kirim per wilayah</b>
        @foreach (\App\Support\DataDummy::ongkir() as $o)
            <span class="bg-[#454545] px-3.5 py-2.5 text-[13px]">{{ $o['wilayah'] }} - Rp {{ number_format($o['biaya'], 0, ',', '.') }}</span>
        @endforeach
        <span class="bg-[#454545] px-3.5 py-2.5 text-[13px]">Ambil di toko - Gratis</span>
    @endunless

    <a href="https://wa.me/628585094077" target="_blank" class="ml-auto font-semibold">Kontak: +62 858-5094-2077</a>
</footer>