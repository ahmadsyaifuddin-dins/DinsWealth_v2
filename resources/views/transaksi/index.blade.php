<x-app-layout>
    <x-slot name="header">
        {{ __('Daftar Transaksi') }}
    </x-slot>

    <div class="p-4 bg-white rounded-lg shadow-xs">

        <div class="flex justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-700">Riwayat Keuangan</h2>
            <a href="{{ route('transaksi.create') }}"
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                + Tambah Transaksi
            </a>
        </div>

        <div class="inline-flex overflow-hidden mb-4 w-full bg-white rounded-lg shadow-md">
            <div class="flex justify-center items-center w-12 bg-blue-500">
                <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M20 3.33331C10.8 3.33331 3.33337 10.8 3.33337 20C3.33337 29.2 10.8 36.6666 20 36.6666C29.2 36.6666 36.6667 29.2 36.6667 20C36.6667 10.8 29.2 3.33331 20 3.33331ZM16.9997 25.326L12.833 21.166L15.1897 18.81L16.9997 20.62L24.8103 12.81L27.1663 15.166L16.9997 25.326Z">
                    </path>
                </svg>
            </div>

            <div class="px-4 py-2 -mx-3">
                <div class="mx-3">
                    <span class="font-semibold text-blue-500">Data Transaksi</span>
                    <p class="text-sm text-gray-600">Semua riwayat pemasukan dan pengeluaranmu.</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden mb-8 w-full rounded-lg border shadow-xs">
            <div class="overflow-x-auto w-full">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase bg-gray-50 border-b">
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">Akun</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y">
                        @foreach ($transaksi as $item)
                            <tr class="text-gray-700">
                                <td class="px-4 py-3 text-sm">
                                    {{ $item->tanggal_transaksi->format('d M Y') }}
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    <div class="font-semibold">{{ $item->kategori->nama ?? 'Tanpa Kategori' }}</div>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ Str::limit($item->keterangan, 30) }}
                                </td>

                                <td class="px-4 py-3 text-xs">
                                    @if ($item->jenis == 'pemasukan')
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                            Pemasukan
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-sm font-bold text-right">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ $item->akun->nama ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div
                class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase bg-gray-50 border-t sm:grid-cols-9">
                {{ $transaksi->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
