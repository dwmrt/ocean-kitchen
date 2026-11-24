<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBuktiRequest extends FormRequest
{
    /** Ukuran maksimal bukti (KB). 5 MB cukup untuk tangkapan layar maupun foto struk dari HP. */
    public const MAKS_KB = 5120;

    public function authorize(): bool
    {
        return true;   // pelanggan tanpa login; kode pesanan berfungsi sebagai kunci
    }

    /**
     * "mimes" dan "mimetypes" sama-sama membaca ISI file (bukan hanya namanya),
     * jadi file berbahaya yang diberi ekstensi .jpg tetap ditolak. SVG tidak diizinkan.
     */
    public function rules(): array
    {
        return [
            'bukti_bayar' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png',
                'mimetypes:image/jpeg,image/png',
                'max:'.self::MAKS_KB,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_bayar.required' => 'Pilih foto bukti pembayaran terlebih dahulu.',
            'bukti_bayar.file' => 'Bukti pembayaran harus berupa file.',
            'bukti_bayar.uploaded' => 'Unggahan gagal. Ukuran file mungkin terlalu besar (maksimal 5 MB).',
            'bukti_bayar.mimes' => 'Bukti pembayaran harus berupa foto JPG atau PNG.',
            'bukti_bayar.mimetypes' => 'Bukti pembayaran harus berupa foto JPG atau PNG.',
            'bukti_bayar.max' => 'Ukuran foto terlalu besar (maksimal 5 MB).',
        ];
    }
}
