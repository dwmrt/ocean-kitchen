<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HitungTotalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // halaman publik, pelanggan tanpa login
    }

    /**
     * Hanya bentuk datanya yang dicek di sini. Aturan yang butuh database
     * (produk aktif, minimal order, tingkat jarak) dicek di HitungTotalService.
     * Harga TIDAK diterima dari browser: selalu diambil dari database.
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id_produk' => ['required', 'integer'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:1000'],
            'metode_terima' => ['required', 'in:ambil,antar'],
            'id_tarif' => ['nullable', 'required_if:metode_terima,antar', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pilih minimal satu produk.',
            'items.array' => 'Daftar produk tidak valid.',
            'items.min' => 'Pilih minimal satu produk.',
            'items.max' => 'Terlalu banyak jenis produk dalam satu pesanan.',
            'items.*.id_produk.required' => 'Produk tidak valid.',
            'items.*.id_produk.integer' => 'Produk tidak valid.',
            'items.*.jumlah.required' => 'Jumlah wajib diisi.',
            'items.*.jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'items.*.jumlah.max' => 'Jumlah terlalu banyak.',
            'metode_terima.required' => 'Pilih metode terima: ambil di rumah atau antar.',
            'metode_terima.in' => 'Metode terima tidak valid.',
            'id_tarif.required_if' => 'Pilih tingkat jarak untuk pengantaran.',
            'id_tarif.integer' => 'Tingkat jarak tidak valid.',
        ];
    }
}
