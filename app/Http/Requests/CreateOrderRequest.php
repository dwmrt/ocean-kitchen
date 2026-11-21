<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // pelanggan tanpa login
    }

    /** Rapikan input sebelum dicek: nomor HP jadi 08..., jadwal dari "T" jadi spasi. */
    protected function prepareForValidation(): void
    {
        $hp = preg_replace('/[\s\-\(\)\.]/', '', (string) $this->input('no_hp'));

        if (str_starts_with($hp, '+62')) {
            $hp = '0'.substr($hp, 3);
        } elseif (str_starts_with($hp, '62')) {
            $hp = '0'.substr($hp, 2);
        }

        $this->merge([
            'no_hp' => $hp,
            // datetime-local di browser mengirim "2026-11-27T14:00"
            'jadwal_terima' => str_replace('T', ' ', (string) $this->input('jadwal_terima')),
        ]);
    }

    /**
     * Hanya bentuk datanya. Aturan yang butuh database (PO dibuka, tanggal terima,
     * produk aktif, minimal order, tingkat jarak) dicek di CreateOrderService.
     * Total, ongkir, dan harga TIDAK diterima dari browser.
     */
    public function rules(): array
    {
        return [
            'nama_pelanggan' => ['required', 'string', 'min:2', 'max:100'],
            'no_hp' => ['required', 'regex:/^08[0-9]{8,12}$/'],
            'metode_terima' => ['required', 'in:ambil,antar'],
            'alamat' => ['nullable', 'required_if:metode_terima,antar', 'string', 'min:10', 'max:500'],
            'id_tarif' => ['nullable', 'required_if:metode_terima,antar', 'integer'],
            'jadwal_terima' => ['required', 'date_format:Y-m-d H:i'],
            'metode_bayar' => ['required', 'in:non_tunai,cod'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.id_produk' => ['required', 'integer'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_pelanggan.required' => 'Nama wajib diisi.',
            'nama_pelanggan.min' => 'Nama terlalu pendek.',
            'nama_pelanggan.max' => 'Nama terlalu panjang.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'metode_terima.required' => 'Pilih metode terima: ambil di rumah atau antar.',
            'metode_terima.in' => 'Metode terima tidak valid.',
            'alamat.required_if' => 'Alamat lengkap wajib diisi untuk pengantaran.',
            'alamat.min' => 'Alamat terlalu pendek. Tulis lengkap beserta patokan.',
            'id_tarif.required_if' => 'Pilih tingkat jarak untuk pengantaran.',
            'id_tarif.integer' => 'Tingkat jarak tidak valid.',
            'jadwal_terima.required' => 'Jadwal terima wajib diisi.',
            'jadwal_terima.date_format' => 'Format jadwal terima tidak valid.',
            'metode_bayar.required' => 'Pilih metode pembayaran.',
            'metode_bayar.in' => 'Metode pembayaran tidak valid.',
            'items.required' => 'Pilih minimal satu produk.',
            'items.min' => 'Pilih minimal satu produk.',
            'items.array' => 'Daftar produk tidak valid.',
            'items.*.jumlah.min' => 'Jumlah minimal 1.',
            'items.*.jumlah.integer' => 'Jumlah harus berupa angka bulat.',
        ];
    }
}
