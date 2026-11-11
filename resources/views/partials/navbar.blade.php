<header class="bg-brand text-white px-[5%] py-2 flex flex-wrap items-center justify-between gap-2">
    <a href="/" class="flex items-center gap-3 font-display font-bold text-[22px] uppercase">
        <img src="{{ asset('images/logo.png') }}" alt="" class="w-12 h-12 rounded-full">
        Ocean Kitchen
    </a>
    <nav class="flex flex-wrap gap-7">
        <a href="/" class="py-1 opacity-80 hover:opacity-100 {{ request()->is('/') ? '!opacity-100 border-b-2 border-white' : '' }}">Beranda</a>
        <a href="/#katalog" class="py-1 opacity-80 hover:opacity-100">Katalog</a>
        <a href="/#cara-pesan" class="py-1 opacity-80 hover:opacity-100">Cara Pesan</a>
        <a href="#ongkir" class="py-1 opacity-80 hover:opacity-100">Info Ongkir</a>
        <a href="#" class="py-1 opacity-80 hover:opacity-100">Cek Pesanan</a>
    </nav>
</header>