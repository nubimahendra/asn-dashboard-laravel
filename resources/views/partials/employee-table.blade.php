<div class="overflow-x-auto">
    <table class="w-full text-left text-sm text-gray-600 dark:text-gray-400">
        <thead class="bg-gray-50 dark:bg-gray-900/50 text-xs uppercase font-semibold text-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-4">No</th>
                <th class="px-6 py-4">Nama Pegawai</th>
                <th class="px-6 py-4">Jabatan</th>
                <th class="px-6 py-4">Unit Kerja</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Pendidikan</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($pegawai as $index => $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">{{ $pegawai->firstItem() + $index }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">{{ $p->nama_pegawai }}</td>
                        <td class="px-6 py-4">{{ $p->jabatan }}</td>
                        <td class="px-6 py-4">{{ $p->pd }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ str_contains($p->sts_peg, 'PNS') ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                (str_contains($p->sts_peg, 'PPPK') ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                {{ $p->sts_peg }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $p->tk_pend }}</td>
                    </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        Tidak ada data pegawai ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
    {{ $pegawai->links() }}
</div>