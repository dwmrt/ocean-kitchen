<?php

namespace App\Http\Controllers;

use App\Models\PeriodePo;
use App\Models\Produk;
use App\Models\TarifOngkir;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    /** Urutan tab kategori di halaman katalog. */
    private const URUTAN_KATEGORI = [
        'Cake', 'Brownies', 'Cookies & Kue Kering', 'Donat', 'Pie', 'Minuman',
    ];

    /**
     * GetKatalog(): daftar produk aktif dari database, bisa difilter per kategori.
     * Bentuk data sengaja dibuat sama dengan DataDummy supaya view tidak perlu dibongkar.
     */
    public function getKatalog(Request $request): View
    {
        $semua = Produk::aktif()->orderBy('id_produk')->get();

        $tersedia = $semua->pluck('kategori')->unique();
        $urutan = collect(self::URUTAN_KATEGORI);

        $kategoriList = array_merge(
            ['Semua'],
            $urutan->filter(fn ($k) => $tersedia->contains($k))->values()->all(),
            $tersedia->diff($urutan)->sort()->values()->all(),   // kategori baru di luar urutan
        );

        $kategori = $request->query('kategori', 'Semua');
        if (! in_array($kategori, $kategoriList, true)) {
            $kategori = 'Semua';
        }

        $produk = $semua
            ->when($kategori !== 'Semua', fn ($c) => $c->where('kategori', $kategori))
            ->map(fn (Produk $p) => $this->keKartu($p))
            ->values();

        return view('katalog', [
            'kategoriList' => $kategoriList,
            'kategori' => $kategori,
            'produk' => $produk,
            'po' => $this->dataPo(),
            'ongkir' => $this->dataOngkir(),
        ]);
    }

    /**
     * GetDetailProduk(): detail satu produk aktif. 404 kalau nonaktif atau tidak ada.
     */
    public function getDetailProduk(int $id): View
    {
        $produk = Produk::aktif()->findOrFail($id);

        return view('produk.detail', [
            'produk' => $this->keKartu($produk),
            'po' => $this->dataPo(),
        ]);
    }

    /** Bentuk data satu produk untuk view (kunci lama + kunci baru). */
    private function keKartu(Produk $p): array
    {
        return [
            // kunci yang sudah dipakai view Elda (sama seperti DataDummy)
            'nama' => $p->nama_produk,
            'deskripsi' => $p->deskripsi,
            'harga' => (int) $p->harga_jual,
            'kategori' => $p->kategori,
            // path relatif dari folder public, pakai asset($p['gambar']) (null kalau belum ada foto)
            'gambar' => $p->foto ? 'storage/'.$p->foto : null,
            // kunci baru
            'id_produk' => $p->id_produk,
            'satuan' => $p->satuan,
            'ukuran' => $p->ukuran,
            'min_order' => $p->min_order,
            'lama_pengerjaan' => $p->lama_pengerjaan,
            'daya_tahan' => $p->daya_tahan,
            'url_detail' => route('produk.detail', $p->id_produk),
        ];
    }

    /** Data banner PO. Selalu mengembalikan array supaya view tidak error. */
    private function dataPo(): array
    {
        $po = PeriodePo::terpilih();

        if (! $po) {
            return [
                'nama' => 'Belum ada periode PO',
                'buka' => '-',
                'tutup' => '-',
                'diterima' => '-',
                'dibuka' => false,
            ];
        }

        return [
            'nama' => $po->nama_periode,
            'buka' => $po->tanggal_buka->translatedFormat('j M Y'),
            'tutup' => $po->tanggal_tutup->translatedFormat('j M Y'),
            'diterima' => $po->tanggal_terima->translatedFormat('j M Y'),
            'dibuka' => $po->dibuka,   // dihitung di server dari tanggal + status
        ];
    }

    /** Tarif ongkir. "biaya" = biaya per kg (lihat "satuan"). */
    private function dataOngkir(): array
    {
        return TarifOngkir::orderBy('id_tarif')->get()
            ->map(fn (TarifOngkir $t) => [
                'wilayah' => $t->wilayah,
                'biaya' => (int) $t->biaya,
                'satuan' => 'per kg',
            ])
            ->all();
    }
}
