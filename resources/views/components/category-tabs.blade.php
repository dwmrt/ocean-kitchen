@props(['list', 'aktif', 'route' => 'home'])

<div class="flex overflow-x-auto border-y border-ink">
    @foreach ($list as $k)
        <a href="{{ route($route, ['kategori' => $k]) }}#katalog"
           class="flex-1 text-center py-3 whitespace-nowrap {{ $aktif === $k ? 'text-brand font-semibold border-b-2 border-brand' : 'text-muted' }}">
            {{ $k }}
        </a>
    @endforeach
</div>