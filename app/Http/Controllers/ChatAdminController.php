<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChatMessage;
use App\Models\SnapshotPegawai;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatAdminController extends Controller
{
    private function getChatList()
    {
        // Group logic needs to handle both 'sender_number' (WA) and 'user_id' (Web)
        // Since groupBy in strict mode can be tricky with mixed nulls, we might do separate queries or a UNION.
        // For simplicity given the scope:

        // 1. Get WhatsApp Chats (grouped by sender_number)
        $waChats = ChatMessage::where('source', 'whatsapp')
            ->select('sender_number', 'source', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('sender_number', 'source')
            ->orderBy('last_message_at', 'desc')
            ->get();

        foreach ($waChats as $chat) {
            $chat->identifier = $chat->sender_number;
            $chat->pegawai = SnapshotPegawai::where('no_hp', $chat->sender_number)->first();
            $chat->display_name = $chat->pegawai ? $chat->pegawai->nama_pegawai : $chat->sender_number;
            $chat->latest_message = ChatMessage::where('sender_number', $chat->sender_number)
                ->latest()
                ->first();
        }

        // 2. Get Web Chats (grouped by user_id)
        $webChats = ChatMessage::where('source', 'web')
            ->select('user_id', 'source', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('user_id', 'source')
            ->orderBy('last_message_at', 'desc')
            ->get();

        foreach ($webChats as $chat) {
            $chat->identifier = 'WEB-' . $chat->user_id; // Unique ID for frontend
            $user = \App\Models\User::find($chat->user_id);

            $chat->pegawai = null;
            if ($user && $user->nip) {
                $chat->pegawai = SnapshotPegawai::where('nip_baru', $user->nip)->first();
            }

            if ($chat->pegawai) {
                $chat->display_name = $chat->pegawai->nama_pegawai . ' (' . $chat->pegawai->nip_baru . ')';
            } else {
                $chat->display_name = 'Tamu / Belum Verifikasi';
            }

            $chat->latest_message = ChatMessage::where('user_id', $chat->user_id)
                ->where('source', 'web')
                ->latest()
                ->first();
        }

        // Merge and Sort
        $allChats = $waChats->merge($webChats)->sortByDesc('last_message_at');
        return $allChats;
    }

    public function index()
    {
        $chats = $this->getChatList();
        return view('admin.chat.index', compact('chats'));
    }

    public function show(Request $request, $identifier)
    {
        // Parse identifier
        $isWeb = str_starts_with($identifier, 'WEB-');
        $messages = collect([]);
        $pegawai = null;

        if ($isWeb) {
            $userId = str_replace('WEB-', '', $identifier);
            $messages = ChatMessage::where('user_id', $userId)
                ->where('source', 'web')
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark read
            ChatMessage::where('user_id', $userId)
                ->where('source', 'web')
                ->where('is_read', false)
                ->where('direction', 'in')
                ->update(['is_read' => true]);

            $user = \App\Models\User::find($userId);
            if ($user && $user->nip) {
                $pegawai = SnapshotPegawai::where('nip_baru', $user->nip)->first();
            }
        } else {
            $phoneNumber = $identifier; // WA Number
            $messages = ChatMessage::where('sender_number', $phoneNumber)
                ->where('source', 'whatsapp')
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark read
            ChatMessage::where('sender_number', $phoneNumber)
                ->where('is_read', false)
                ->where('direction', 'in')
                ->update(['is_read' => true]);

            $pegawai = SnapshotPegawai::where('no_hp', $phoneNumber)->first();
        }

        if ($request->ajax()) {
            return view('admin.chat.partials.conversation', compact('messages', 'pegawai', 'identifier'));
        }

        $chats = $this->getChatList();
        return view('admin.chat.index', compact('chats', 'messages', 'pegawai', 'identifier'));
    }

    public function reply(Request $request)
    {
        $identifier = $request->input('phone_number'); // Reuse input name
        $message = $request->input('message');

        $isWeb = str_starts_with($identifier, 'WEB-');

        if ($isWeb) {
            $userId = str_replace('WEB-', '', $identifier);
            $newMessage = ChatMessage::create([
                'user_id' => $userId,
                'source' => 'web',
                'message' => $message,
                'direction' => 'out',
                'is_handled_by_bot' => false,
                'is_read' => true,
            ]);
        } else {
            $phoneNumber = $identifier;
            $this->sendToWA($phoneNumber, $message);
            $newMessage = ChatMessage::create([
                'sender_number' => $phoneNumber,
                'source' => 'whatsapp',
                'message' => $message,
                'direction' => 'out',
                'is_handled_by_bot' => false,
                'is_read' => true,
            ]);
        }

        if ($request->expectsJson()) {
            $html = view('admin.chat.partials.bubble', ['message' => $newMessage])->render();
            return response()->json(['status' => 'success', 'html' => $html]);
        }

        return back()->with('success', 'Balasan terkirim');
    }

    private function sendToWA($target, $message)
    {
        // Use ChatService or existing logic to send to WA API
        // Ambil token aktif
        $tokenRecord = \App\Models\FonnteToken::where('is_active', true)->latest()->first();

        if ($tokenRecord && $tokenRecord->token) {
            try {
                \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $tokenRecord->token,
                ])->post('https://api.fonnte.com/send', [
                            'target' => $target,
                            'message' => $message,
                            'countryCode' => '62',
                        ]);
            } catch (\Exception $e) {
                Log::error("Gagal mengirim pesan ke Fonnte (Reply): " . $e->getMessage());
            }
        }
    }
}
