<?php

namespace App\Services;

use App\Models\Faq;
use App\Models\SnapshotPegawai;
use App\Models\User;

class ChatService
{
    /**
     * Dapatkan respon bot berdasarkan pesan user.
     * 
     * @param string $message
     * @param User $user
     * @return string
     */
    public function getBotResponse(string $message, User $user): string
    {
        // 1. Cek NIP di profil User
        if (empty($user->nip)) {
            // Coba cek apakah message ini adalah NIP (angka 18 digit)
            if (preg_match('/^\d{18}$/', $message)) {
                return $this->verifyAndSaveNip($message, $user);
            }
            return "Halo! Untuk memulai layanan, mohon ketikkan NIP Anda (18 digit).";
        }

        // 2. Cari jawaban di FAQ
        // Menggunakan scopeFindByKeyword dari model FAQ yang sudah ada (pastikan logicnya aman)
        // Atau kita buat logic simple di sini.
        // Asumsi Faq model punya scopeFindByKeyword atau kita query manual.

        // Cek dulu apakah model FAQ punya scopeFindByKeyword yang kita fix sebelumnya (removed is_active)
        // Kita gunakan pencarian sederhana saja agar robust.
        $faq = Faq::where(function ($query) use ($message) {
            $words = explode(' ', strtolower($message));
            foreach ($words as $word) {
                if (strlen($word) > 2) {
                    $query->orWhere('keywords', 'LIKE', "%{$word}%");
                }
            }
        })->first();

        if ($faq) {
            return $faq->answer;
        }

        return "Mohon maaf, saya belum mengerti pertanyaan Anda. Pertanyaan ini akan diteruskan ke Admin.";
    }

    private function verifyAndSaveNip(string $nip, User $user): string
    {
        $pegawai = SnapshotPegawai::where('nip_baru', $nip)->first();

        if ($pegawai) {
            $user->update(['nip' => $nip]);
            return "Terima kasih Bpk/Ibu {$pegawai->nama_pegawai}, identitas Anda terverifikasi. Ada yang bisa kami bantu?";
        }

        return "Maaf, NIP tidak ditemukan dalam data pegawai. Mohon periksa kembali.";
    }
}
