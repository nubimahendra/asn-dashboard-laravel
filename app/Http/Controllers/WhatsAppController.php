<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SnapshotPegawai;
use App\Models\Faq;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $sender = $request->input('sender'); // Phone number
        $message = $request->input('message'); // Text

        // 1. Verify Sender
        $pegawai = SnapshotPegawai::where('no_hp', $sender)->first();
        if (!$pegawai) {
            // Option: Log unknown sender or return
            return response()->json(['status' => 'ignored', 'message' => 'Sender not found'], 200);
        }

        // 2. Hybrid Logic - FAQ Search
        // Simple naive search: check if message contains keyword
        $faq = Faq::where('keyword', 'LIKE', '%' . $message . '%')->first();

        if ($faq) {
            // FAQ Found -> Auto Reply

            // Save Incoming
            ChatMessage::create([
                'sender_number' => $sender,
                'message' => $message,
                'direction' => 'in',
                'is_handled_by_bot' => true,
                'is_read' => true, // Auto-handled, so effectively read
            ]);

            // Send Reply
            $this->sendToWA($sender, $faq->answer);

            // Save Outgoing
            ChatMessage::create([
                'sender_number' => $sender,
                'message' => $faq->answer,
                'direction' => 'out',
                'is_handled_by_bot' => true,
                'is_read' => true,
            ]);

        } else {
            // FAQ Not Found -> Save for Admin
            ChatMessage::create([
                'sender_number' => $sender,
                'message' => $message,
                'direction' => 'in',
                'is_handled_by_bot' => false,
                'is_read' => false,
            ]);
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function sendToWA($target, $message)
    {
        // Placeholder for calling Fonnte or other API
        // Http::post('https://api.fonnte.com/send', [...]);
        Log::info("Sending WA to $target: $message");
    }
}
