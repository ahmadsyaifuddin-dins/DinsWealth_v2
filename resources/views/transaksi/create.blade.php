<x-app-layout>
    <x-slot name="header">
        {{ __('Tambah Transaksi Baru') }}
    </x-slot>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">

        <form action="{{ route('transaksi.store') }}" method="POST">
            @csrf

            <label class="block text-sm">
                <span class="text-gray-700">Tanggal Transaksi</span>
                <input type="date" name="tanggal_transaksi" value="{{ date('Y-m-d') }}"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                    required />
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Jenis Transaksi</span>
                <select name="jenis"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select">
                    <option value="pengeluaran">Pengeluaran (Uang Keluar)</option>
                    <option value="pemasukan">Pemasukan (Uang Masuk)</option>
                </select>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Kategori</span>
                <select name="id_kategori"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select">
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id }}">
                            {{ $kat->nama }} ({{ ucfirst($kat->jenis) }})
                        </option>
                    @endforeach
                </select>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Sumber Dana / Akun</span>
                <select name="id_akun"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-select">
                    @foreach ($akun as $ak)
                        <option value="{{ $ak->id }}">{{ $ak->nama }} (Sisa: Rp
                            {{ number_format($ak->saldo_saat_ini) }})</option>
                    @endforeach
                </select>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Nominal (Rp)</span>
                <input type="number" name="nominal" placeholder="Contoh: 15000"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                    required />
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700">Keterangan / Catatan</span>
                <textarea name="keterangan" rows="3"
                    class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-textarea"
                    placeholder="Makan siang nasi padang..."></textarea>
            </label>

            <div class="mt-6">
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Simpan Transaksi
                </button>

                <a href="{{ route('transaksi.index') }}"
                    class="ml-2 px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 bg-gray-200 border border-transparent rounded-lg active:bg-gray-300 hover:bg-gray-300 focus:outline-none focus:shadow-outline-gray">
                    Batal
                </a>
            </div>

        </form>
    </div>
</x-app-layout>
