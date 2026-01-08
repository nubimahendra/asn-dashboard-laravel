<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\SnapshotPegawai;
use Carbon\Carbon;
use Exception;

class PegawaiSyncService
{
    public function sync()
    {
        try {
            $data = DB::connection('sidawai')->table('export_pegawai')
                ->select('nip_baru', 'nama_pegawai', 'eselon', 'jabatan', 'pd', 'sub_pd', 'jenikel', 'sts_peg', 'tk_pend')
                ->get();

            if ($data->isEmpty()) {
                return [
                    'status' => 'warning',
                    'message' => 'No data found in source database.'
                ];
            }

            DB::transaction(function () use ($data) {
                SnapshotPegawai::query()->delete();

                // Convert collection to array for chunking
                $chunks = $data->chunk(100);
                $timestamp = Carbon::now();

                foreach ($chunks as $chunk) {
                    $insertData = [];
                    foreach ($chunk as $row) {
                        $insertData[] = [
                            'nip_baru' => $row->nip_baru,
                            'nama_pegawai' => $row->nama_pegawai,
                            'eselon' => $row->eselon,
                            'jabatan' => $row->jabatan,
                            'pd' => $row->pd,
                            'sub_pd' => $row->sub_pd,
                            'jenikel' => $row->jenikel,
                            'sts_peg' => $row->sts_peg,
                            'tk_pend' => $row->tk_pend,
                            'last_sync_at' => $timestamp,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }
                    SnapshotPegawai::insert($insertData);
                }
            });

            return [
                'status' => 'success',
                'count' => $data->count(),
                'message' => 'Data successfully synced.'
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }
}
