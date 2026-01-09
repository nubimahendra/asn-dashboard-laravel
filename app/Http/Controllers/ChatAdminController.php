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
        $chats = ChatMessage::select('sender_number', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('sender_number')
            ->orderBy('last_message_at', 'desc')
            ->get();

        foreach ($chats as $chat) {
            $chat->pegawai = SnapshotPegawai::where('no_hp', $chat->sender_number)->first();
            $chat->latest_message = ChatMessage::where('sender_number', $chat->sender_number)
                ->latest()
                ->first();
        }
        return $chats;
    }

    public function index()
    {
        $chats = $this->getChatList();
        return view('admin.chat.index', compact('chats'));
    }

    public function show(Request $request, $phoneNumber)
    {
        $messages = ChatMessage::where('sender_number', $phoneNumber)
            ->orderBy('created_at', 'asc')
            ->get();

        $pegawai = SnapshotPegawai::where('no_hp', $phoneNumber)->first();

        // Mark unread as read
        ChatMessage::where('sender_number', $phoneNumber)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->ajax()) {
            return view('admin.chat.partials.conversation', compact('messages', 'pegawai', 'phoneNumber'));
        }

        // Non-AJAX: Return full view with sidebar
        $chats = $this->getChatList();
        return view('admin.chat.index', compact('chats', 'messages', 'pegawai', 'phoneNumber'));
    }

    public function reply(Request $request)
    {
        $phoneNumber = $request->input('phone_number');
        $message = $request->input('message');

        $this->sendToWA($phoneNumber, $message);

        $newMessage = ChatMessage::create([
            'sender_number' => $phoneNumber,
            'message' => $message,
            'direction' => 'out',
            'is_handled_by_bot' => false,
            'is_read' => true,
        ]);

        if ($request->expectsJson()) {
            $html = view('admin.chat.partials.bubble', ['message' => $newMessage])->render();
            return response()->json(['status' => 'success', 'html' => $html]);
        }

        return back()->with('success', 'Balasan terkirim');
    }

    private function sendToWA($target, $message)
    {
        // Placeholder API
        Log::info("Admin sending WA to $target: $message");
    }
}
