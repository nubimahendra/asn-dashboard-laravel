<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\SnapshotPegawai;
use App\Models\Faq;
use App\Models\ChatMessage;
use App\Models\FonnteToken;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $chatService;

    public function __construct(\App\Services\ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function handleWebhook(Request $request)
    {
        $sender = $request->input('sender'); // Format Fonnte: 62812xxx
        $message = $request->input('message'); // Text message

        // Gunakan Service untuk memproses logic (Cari user, simpan pesan, cari FAQ, simpan balasan)
        // Identifier = $sender
        // Source = 'whatsapp'
        // UserId = null
        $result = $this->chatService->processMessage($sender, $message, 'whatsapp', null);

        $replyMessage = $result['reply_message'];

        // Kirim via Fonnte
        // Ambil token aktif
        $tokenRecord = FonnteToken::where('is_active', true)->latest()->first();

        if ($tokenRecord && $tokenRecord->token) {
            try {
                // Ignore result http request, assume fire and forget or log on error
                Http::withHeaders([
                    'Authorization' => $tokenRecord->token,
                ])->post('https://api.fonnte.com/send', [
                            'target' => $sender,
                            'message' => $replyMessage,
                            'countryCode' => '62',
                        ]);
            } catch (\Exception $e) {
                Log::error("Gagal mengirim pesan ke Fonnte: " . $e->getMessage());
            }
        } else {
            Log::warning("Token Fonnte belum disetting.");
        }

        return response()->json(['status' => 'success'], 200);
    }
}
