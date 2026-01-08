<?php

namespace App\Http\Controllers;

use App\Services\PegawaiSyncService;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    protected $syncService;

    public function __construct(PegawaiSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function sync(Request $request)
    {
        $result = $this->syncService->sync();

        if ($result['status'] === 'success') {
            return back()->with('success', $result['message'] . ' (' . $result['count'] . ' records)');
        } elseif ($result['status'] === 'warning') {
            return back()->with('warning', $result['message']);
        } else {
            return back()->with('error', $result['message']);
        }
    }
}
