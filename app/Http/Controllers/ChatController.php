<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Kirim pesan dari Web Chat.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $user = Auth::user();
        $message = $request->input('message');

        // Gunakan ChatService
        // Identifier = user_id (karena login)
        // Source = 'web'
        // UserId = $user->id
        $result = $this->chatService->processMessage((string) $user->id, $message, 'web', $user->id);

        return response()->json([
            'status' => 'success',
            'reply' => $result['reply_message'],
            'data' => $result
        ]);
    }

    /**
     * Ambil history chat user login.
     */
    public function getHistory(Request $request)
    {
        $user = Auth::user();

        // Check if user has verified NIP
        $isVerified = !empty($user->nip);

        $messages = ChatMessage::where('source', 'web')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'is_verified' => $isVerified,
            'user_name' => $user->name,
            'messages' => $messages
        ]);
    }

    /**
     * Verifikasi NIP User.
     */
    public function verifyNip(Request $request)
    {
        $request->validate([
            'nip' => 'required|numeric'
        ]);

        $nip = $request->input('nip');
        $pegawai = \App\Models\SnapshotPegawai::where('nip_baru', $nip)->first();

        if ($pegawai) {
            // Update User
            $user = Auth::user();
            $user->nip = $nip;
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => "Terima kasih Bpk/Ibu {$pegawai->nama_pegawai}, identitas terverifikasi. Ada yang bisa dibantu?",
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Maaf, NIP tidak terdaftar. Mohon periksa kembali.'
        ], 404);
    }
}
