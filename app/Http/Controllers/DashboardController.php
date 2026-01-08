<?php

namespace App\Http\Controllers;

use App\Models\SnapshotPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 0. Filter Setup (pd = Perangkat Daerah / OPD)
        $filterOpd = $request->input('opd');

        $listOpd = SnapshotPegawai::select('pd')
            ->whereNotNull('pd')
            ->distinct()
            ->orderBy('pd')
            ->pluck('pd');

        $query = SnapshotPegawai::query();

        if ($filterOpd) {
            $query->where('pd', $filterOpd);
        }

        // 1. Top Cards Metrics
        // 1. Top Cards Metrics
        // Note: Calculations need to check for specific string matches or just count
        $totalPegawai = (clone $query)->count();

        // Updated Logic for Jenikel: 1 or L% -> Laki-laki, 2 or P% -> Perempuan
        $totalLaki = (clone $query)->where(function ($q) {
            $q->where('jenikel', 'LIKE', 'L%')
                ->orWhere('jenikel', 'LIKE', '1%')
                ->orWhere('jenikel', 'LIKE', 'Pria%');
        })->count();

        $totalPerempuan = (clone $query)->where(function ($q) {
            $q->where('jenikel', 'LIKE', 'P%')
                ->orWhere('jenikel', 'LIKE', '2%')
                ->orWhere('jenikel', 'LIKE', 'Wanita%');
        })
            ->whereNot('jenikel', 'LIKE', 'Pria%') // Safety check
            ->count();

        // New Summaries: CPNS, PNS, PPPK
        $totalCpns = (clone $query)->where('sts_peg', 'LIKE', '%CPNS%')->count();
        $totalPns = (clone $query)->where('sts_peg', 'LIKE', '%PNS%')->where('sts_peg', 'NOT LIKE', '%CPNS%')->count();
        $totalPppk = (clone $query)->where('sts_peg', 'LIKE', '%PPPK%')->count();

        // 2. Charts Data

        // Chart 1: Jenis Kelamin (Pie)
        // Group by normalized jenikel if possible. 
        // Since we can't easily normalize in query with SQlite/MySQL diffs easily without raw, 
        // let's fetch raw stats and map in PHP.
        $rawJenikel = (clone $query)->select('jenikel', DB::raw('count(*) as total'))
            ->groupBy('jenikel')
            ->pluck('total', 'jenikel');

        $statsJenikel = [
            'Laki-laki' => 0,
            'Perempuan' => 0
        ];

        foreach ($rawJenikel as $key => $val) {
            // Check Laki
            if (str_starts_with($key, '1') || stripos($key, 'L') === 0 || stripos($key, 'Pria') !== false) {
                $statsJenikel['Laki-laki'] += $val;
            }
            // Check Perempuan
            elseif (str_starts_with($key, '2') || stripos($key, 'P') === 0 || stripos($key, 'Wanita') !== false) {
                if (stripos($key, 'Pria') === false) {
                    $statsJenikel['Perempuan'] += $val;
                }
            }
        }

        $chartJenikel = [
            'labels' => array_keys($statsJenikel),
            'series' => array_values($statsJenikel),
        ];

        // Chart 2: Status Pegawai (Pie)
        $statsStsPeg = (clone $query)->select('sts_peg', DB::raw('count(*) as total'))
            ->groupBy('sts_peg')
            ->pluck('total', 'sts_peg');

        $chartStsPeg = [
            'labels' => $statsStsPeg->keys()->toArray(),
            'series' => $statsStsPeg->values()->toArray(),
        ];

        // Chart 3: Pendidikan (tk_pend) (Bar)
        $dataPendidikan = (clone $query)->select('tk_pend', DB::raw('count(*) as total'))
            ->whereNotNull('tk_pend')
            ->groupBy('tk_pend')
            ->orderBy('total', 'desc')
            ->pluck('total', 'tk_pend');

        $chartPendidikan = [
            'categories' => $dataPendidikan->keys()->toArray(),
            'series' => $dataPendidikan->values()->toArray(),
        ];

        // Chart 4: Eselon (Bar) - Replaces Golongan/Jabatan generic
        $dataEselon = (clone $query)->select('eselon', DB::raw('count(*) as total'))
            ->whereNotNull('eselon')
            ->where('eselon', '!=', '')
            ->groupBy('eselon')
            ->orderBy('eselon')
            ->pluck('total', 'eselon');

        $chartEselon = [
            'categories' => $dataEselon->keys()->toArray(),
            'series' => $dataEselon->values()->toArray(),
        ];

        // Chart 5: Unit Kerja (Horizontal Bar - Top 10)
        $dataOpd = (clone $query)->select('pd', DB::raw('count(*) as total'))
            ->whereNotNull('pd')
            ->groupBy('pd')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->pluck('total', 'pd');

        $chartOpd = [
            'categories' => $dataOpd->keys()->toArray(),
            'series' => $dataOpd->values()->toArray(),
        ];

        // Last Sync Info
        $lastSyncRaw = SnapshotPegawai::max('last_sync_at');
        $lastSync = $lastSyncRaw ? Carbon::parse($lastSyncRaw)->format('d M Y H:i') : '-';

        return view('dashboard', compact(
            'listOpd',
            'filterOpd',
            'totalPegawai',
            'totalLaki',
            'totalPerempuan',
            'totalPns',
            'totalCpns',
            'totalPppk',
            'chartJenikel',
            'chartStsPeg',
            'chartPendidikan',
            'chartEselon',
            'chartOpd',
            'lastSync'
        ));
    }
}
