<?php

namespace App\Http\Controllers;

use App\Models\Percakapan;
use App\Models\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatAdminController extends Controller
{
    public function index()
    {
        $admin = Auth::user();

        $percakapanList = Percakapan::with(['pengguna', 'itemProduksi'])
            ->where('id_kategori', $admin->id_kategori)
            ->withCount(['pesan as unread_count' => function ($q) use ($admin) {
                $q->whereNull('dibaca_pada')
                    ->where('id_pengirim', '!=', $admin->id_pengguna);
            }])
            ->orderByDesc('terakhir_aktif')
            ->get();
        return view('test.list-chat-admin', compact('percakapanList'));
    }

    public function show(Percakapan $percakapan)
    {
        $admin = Auth::user();

        $percakapan = Percakapan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_kategori', $admin->id_kategori)
            ->firstOrFail();

        return view('test.detail-chat-admin', [
            'percakapan' => $percakapan,
            'userId' => $admin->id_pengguna,
        ]);
    }

    public function messages(Request $request, Percakapan $percakapan)
    {
        $afterId = (int) $request->query('after_id', 0);

        $admin = Auth::user();

        $percakapan = Percakapan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_kategori', $admin->id_kategori)
            ->firstOrFail();

        $query = $percakapan->pesan()
            ->with('pengirim:id_pengguna,nama_pengguna')
            ->orderBy('id_pesan');

        if ($afterId > 0) {
            $query->where('id_pesan', '>', $afterId);
        }

        $firstUnreadId = null;
        if ($afterId === 0) {
            $firstUnreadId = Pesan::where('id_percakapan', $percakapan->id_percakapan)
                ->whereNull('dibaca_pada')
                ->where('id_pengirim', '!=', $admin->id_pengguna)
                ->min('id_pesan');
        }

        $messages = $query->get();
        $lastId = $messages->last()?->id_pesan ?? $afterId;

        Pesan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_pengirim', '!=', $admin->id_pengguna)
            ->whereNull('dibaca_pada')
            ->when($lastId, fn($q) => $q->where('id_pesan', '<=', $lastId))
            ->update(['dibaca_pada' => now()]);

        return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id_pesan,
                'sender_id' => $m->id_pengirim,
                'text' => $m->isi_pesan,
                'created_at' => $m->created_at->format('Y-m-d H:i'),
                'show_divider_before' => $firstUnreadId && $m->id_pesan === $firstUnreadId,
            ]),
            'last_id' => $lastId,
        ]);
        // $messages = $query->get();

        // Pesan::where('id_percakapan', $percakapan->id_percakapan)
        //     ->where('id_pengirim', '!=', $admin->id_pengguna)
        //     ->whereNull('dibaca_pada')
        //     ->update(['dibaca_pada' => now()]);

        // $lastId = $messages->last() ? $messages->last()->id_pesan : $afterId;

        // return response()->json([
        //     'messages' => $messages->map(function ($m) {
        //         return [
        //             'id' => $m->id_pesan,
        //             'sender_id' => $m->id_pengirim,
        //             'sender_name' => $m->pengirim?->nama_pengguna ?? 'User',
        //             'text' => $m->isi_pesan,
        //             'created_at' => $m->created_at->format('Y-m-d H:i'),
        //         ];
        //     }),
        //     'last_id' => $lastId,
        // ]);
    }

    public function send(Request $request, Percakapan $percakapan)
    {
        $data = $request->validate([
            'isi_pesan' => ['required', 'string', 'max:2000'],
        ]);

        $admin = Auth::user();

        $percakapan = Percakapan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_kategori', $admin->id_kategori)
            ->firstOrFail();

        $pesan = Pesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'id_pengirim' => $admin->id_pengguna,
            'isi_pesan' => $data['isi_pesan'],
        ]);

        $percakapan->update(['terakhir_aktif' => now()]);

        return response()->json([
            'ok' => true,
            'id' => $pesan->id_pesan,
        ]);
    }
}
