<?php

namespace App\Http\Controllers;

use App\Models\ItemProduksi;
use Illuminate\Http\Request;

class CariProdukController extends Controller
{
    // 1. Fungsi untuk dropdown Live Search JS
    public function liveSearch(Request $request)
    {
        $produk = ItemProduksi::where('nama_item', 'LIKE', '%' . $request->q . '%')
                              ->limit(5)
                              ->get(['id_item_produksi', 'nama_item']); 
        return response()->json($produk);
    }

    // 2. Fungsi untuk menampilkan Halaman Grid Hasil Pencarian
    public function search(Request $request)
    {
        $itemProduksi = ItemProduksi::with(['kategoriUsaha', 'detailProduk', 'satuanHarga', 'fotoProduk'])
                                    ->where('nama_item', 'LIKE', '%' . $request->q . '%')
                                    ->get();
                                    
        return view('klien.hasil-pencarian', [
            'title' => 'Hasil Pencarian',
            'itemProduksi' => $itemProduksi,
            'keyword' => $request->q
        ]);
    }

    // 3. Fungsi untuk menampilkan Halaman Detail Produk (Tujuan Akhir)
    // public function detailProduk($id)
    // {
    //     $item = ItemProduksi::with(['kategoriUsaha', 'detailProduk', 'satuanHarga', 'fotoProduk'])->findOrFail($id);
        
    //     return view('klien.detail-produk', [
    //         'title' => 'Detail Produk',
    //         'item' => $item
    //     ]);
    // }
}