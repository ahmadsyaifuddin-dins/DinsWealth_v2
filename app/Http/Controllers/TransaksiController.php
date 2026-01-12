<?php

namespace App\Http\Controllers;

use App\Models\AkunKeuangan;
use App\Models\KategoriTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        // Kita gunakan 'with' agar query lebih cepat (Eager Loading)
        // latest('tanggal_transaksi') agar data terbaru muncul di atas
        $transaksi = Transaksi::with(['kategori', 'akun'])
            ->latest('tanggal_transaksi')
            ->paginate(10);

        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        // Ambil data untuk dropdown
        $kategori = KategoriTransaksi::orderBy('nama')->get();
        $akun = AkunKeuangan::orderBy('nama')->get();

        return view('transaksi.create', compact('kategori', 'akun'));
    }

    // 2. Simpan Data ke Database
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'nominal' => 'required|numeric|min:1',
            'id_kategori' => 'required|exists:kategori_transaksi,id',
            'id_akun' => 'required|exists:akun_keuangan,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Simpan ke Supabase via Eloquent
        $transaksi = Transaksi::create([
            'id_user' => Auth::id(), // Ambil ID user yang sedang login
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'jenis' => $request->jenis,
            'nominal' => $request->nominal,
            'id_kategori' => $request->id_kategori,
            'id_akun' => $request->id_akun,
            'keterangan' => $request->keterangan,
            'adalah_kalibrasi' => false,
        ]);

        // Update Saldo Akun Otomatis
        $akun = AkunKeuangan::find($request->id_akun);
        if ($request->jenis == 'pemasukan') {
            $akun->increment('saldo_saat_ini', $request->nominal);
        } else {
            $akun->decrement('saldo_saat_ini', $request->nominal);
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }
}
