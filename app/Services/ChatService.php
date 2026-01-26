<?php

namespace App\Services;

use App\Models\SnapshotPegawai;
use App\Models\User;
use App\Models\Faq;
use App\Models\ChatMessage;
use Illuminate\Support\Str;

class ChatService
{
    /**
     * Cari user berdasarkan identifier dan source.
     */
    public function findUser(string $identifier, string $source)
    {
        if ($source === 'web') {
            return User::find($identifier);
        }

        if ($source === 'whatsapp') {
            $normalizedPhone = $this->normalizePhoneNumber($identifier);
            return SnapshotPegawai::where('no_hp', $normalizedPhone)->first();
        }

        return null;
    }

    /**
     * Cari jawaban FAQ.
     */
    public function findAnswer(string $message): ?Faq
    {
        return Faq::findByKeyword($message)->first();
    }

    /**
     * Proses pesan utama logic.
     */
    public function processMessage(string $identifier, string $message, string $source, ?int $userId = null)
    {
        // 1. Identifikasi User
        // Note: Untuk 'web', $identifier bisa jadi user_id-nya, atau session ID.
        // Tapi di controller nanti kita pass $userId beneran.
        $userObj = $this->findUser($source === 'web' ? $userId : $identifier, $source);

        $namaUser = 'Bosku';
        if ($userObj) {
            $namaUser = ($source === 'web') ? $userObj->name : $userObj->nama_pegawai;
        }

        // 2. Simpan Pesan Masuk
        $chatMessage = ChatMessage::create([
            'source' => $source,
            'user_id' => $userId, // null jika WA
            'sender_number' => ($source === 'whatsapp') ? $identifier : null, // nomor HP
            'message' => $message,
            'direction' => 'in',
            'is_handled_by_bot' => false,
            'is_read' => false,
        ]);

        // 3. Cari Jawaban
        $faq = $this->findAnswer($message);

        $handledByBot = false;
        $replyMessage = "";

        if ($faq) {
            $replyMessage = $this->formatResponse($namaUser, $faq->answer, true);
            $handledByBot = true;

            // Update pesan masuk jadi handled
            $chatMessage->update([
                'is_handled_by_bot' => true,
                'is_read' => true
            ]);
        } else {
            $replyMessage = $this->formatResponse($namaUser, null, false);
            // Default: handledByBot = true (karena bot bales "maaf"), atau false jika ingin admin notify.
            // Kita set true sebagai tanda "bot sudah merespon".
            $handledByBot = true;
        }

        // 4. Simpan Pesan Balasan (OUT)
        // Kita tidak kirim langsung di sini utk WA (karena butuh HTTP request), 
        // tapi kita return datanya biar Controller yang handle pengiriman spesifik (misal hit API Fonnte).
        // TAPI, untuk keseragaman database, kita create record 'out' di sini juga boleh,
        // ATAU controller yang create setelah sukses kirim.
        // Agar "Brain" ini lengkap, kita create record di sini. 
        // Nanti Controller tinggal "kirim" ke tujuan.

        $outMessage = ChatMessage::create([
            'source' => $source,
            'user_id' => $userId,
            'sender_number' => ($source === 'whatsapp') ? $identifier : null,
            'message' => $replyMessage,
            'direction' => 'out',
            'is_handled_by_bot' => $handledByBot,
            'is_read' => true,
        ]);

        return [
            'reply_message' => $replyMessage,
            'user' => $userObj,
            'faq_found' => (bool) $faq,
            'out_message_id' => $outMessage->id
        ];
    }

    /*
     * Format Text Balasan
     */
    public function formatResponse($namaUser, $answerText, $isFound)
    {
        if ($isFound) {
            return "Halo *$namaUser*, berikut jawaban untuk pertanyaan Anda:\n\n" . $answerText;
        }
        return "Halo *$namaUser*, mohon maaf saya belum menemukan info terkait hal tersebut. Percakapan ini akan dialihkan ke Admin.";
    }

    /**
     * Normalisasi nomor HP dari format 628... ke 08...
     */
    public function normalizePhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (Str::startsWith($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }
        return $phone;
    }
}
