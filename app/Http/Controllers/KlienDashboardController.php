<?php

namespace App\Http\Controllers;

use App\Models\KategoriUsaha;
use App\Models\ItemProduksi;
use Illuminate\Http\Request;

class KlienDashboardController extends Controller
{
    public function index(Request $request)
    {
        $idKategori = $request->kategori;

        $kategoriUsaha = KategoriUsaha::orderBy('nama_kategori')->get();

        $produk = ItemProduksi::query()
            ->with('kategoriUsaha')
            ->when($idKategori, function ($query) use ($idKategori) {
                $query->where('id_kategori', $idKategori);
            })
            ->orderBy('nama_item_produksi')
            ->get();

        $data = [
            'title' => 'Dashboard Klien',
            'kategoriUsaha' => $kategoriUsaha,
            'produk' => $produk,
            'idKategoriAktif' => $idKategori,
        ];

        return view('klien.dashboard', $data);
    }
}