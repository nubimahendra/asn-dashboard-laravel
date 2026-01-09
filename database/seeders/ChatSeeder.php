<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChatMessage;
use App\Models\Faq;
use Carbon\Carbon;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to prevent duplicates if run multiple times (optional, but good for dev)
        // ChatMessage::truncate(); 
        // Faq::truncate();

        // Seed FAQs
        $faqs = [
            ['keyword' => 'syarat', 'answer' => 'Syarat pengajuan cuti adalah: 1. Formulir, 2. Persetujuan Atasan.'],
            ['keyword' => 'jam', 'answer' => 'Jam kerja pelayanan adalah Senin-Jumat pukul 08.00 - 15.00 WIB.'],
            ['keyword' => 'lokasi', 'answer' => 'Kantor kami berlokasi di Jl. Merdeka No. 1, Blitar.'],
            ['keyword' => 'kontak', 'answer' => 'Anda dapat menghubungi kami via telepon di (0342) 123456.'],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(['keyword' => $faq['keyword']], $faq);
        }

        // Seed Chat Messages (Conversations)

        // Conversation 1: Pak Budi (Active, Unread)
        $number1 = '6281234567890';
        $msgs1 = [
            ['sender_number' => $number1, 'message' => 'Halo min, mau tanya info cuti', 'direction' => 'in', 'is_read' => true, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subHours(2)],
            ['sender_number' => $number1, 'message' => 'Syarat pengajuan cuti adalah: 1. Formulir, 2. Persetujuan Atasan.', 'direction' => 'out', 'is_read' => true, 'is_handled_by_bot' => true, 'created_at' => Carbon::now()->subHours(1)->addMinutes(59)], // Auto-reply
            ['sender_number' => $number1, 'message' => 'Kalau formnya ambil dimana catatannya?', 'direction' => 'in', 'is_read' => false, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subMinutes(30)],
        ];

        // Conversation 2: Bu Siti (History, Read)
        $number2 = '6289876543210';
        $msgs2 = [
            ['sender_number' => $number2, 'message' => 'Selamat pagi', 'direction' => 'in', 'is_read' => true, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subDays(1)->hour(9)],
            ['sender_number' => $number2, 'message' => 'Apakah hari ini buka?', 'direction' => 'in', 'is_read' => true, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subDays(1)->hour(9)->addMinute()],
            ['sender_number' => $number2, 'message' => 'Iya bu, hari ini kami buka seperti biasa.', 'direction' => 'out', 'is_read' => true, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subDays(1)->hour(9)->addMinutes(5)],
            ['sender_number' => $number2, 'message' => 'Terima kasih informasinya', 'direction' => 'in', 'is_read' => true, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subDays(1)->hour(9)->addMinutes(10)],
        ];

        // Conversation 3: Unknown (New Trigger)
        $number3 = '628555000111';
        $msgs3 = [
            ['sender_number' => $number3, 'message' => 'Tes chatbot', 'direction' => 'in', 'is_read' => false, 'is_handled_by_bot' => false, 'created_at' => Carbon::now()->subMinutes(5)],
        ];

        foreach (array_merge($msgs1, $msgs2, $msgs3) as $msg) {
            ChatMessage::create($msg);
        }
    }
}
