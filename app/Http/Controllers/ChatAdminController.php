<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;

class ChatAdminController extends Controller
{
    public function index()
    {
        // Initial data loading for the view
        // Ideally we fetch via API for dynamic feel, but let's pass initial state
        // or just let the view fetch it via JS to be consistent with "Vanilla JS Refactor"
        return view('admin.chat.index');
    }
}
