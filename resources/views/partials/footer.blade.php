<footer id="ongkir" class="bg-dark text-white px-[5%] py-6 flex flex-wrap items-center gap-5 mt-10">
    @unless (request()->routeIs('katalog'))
        <b>Informasi ongkos kirim</b>
        <a href="{{ route('katalog') }}#tarif-ongkir" class="underline">Lihat tarif ongkir</a>
    @endunless

    <a href="https://wa.me/628585094077" target="_blank" class="ml-auto font-semibold">Kontak: +62 858-5094-2077</a>
</footer>