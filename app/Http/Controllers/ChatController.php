<?php

namespace App\Http\Controllers;

use App\Models\Percakapan;
use App\Models\Pesan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function show($id)
    {
        $userId = Auth::id();

        $percakapan = Percakapan::with(['itemProduksi.kategoriUsaha'])
            ->where('id_percakapan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        return view('test.detail-chat-klien', [
            'percakapan' => $percakapan,
            'userId' => $userId,
        ]);
    }


    public function index()
    {
        $userId = Auth::id();

        $percakapanList = Percakapan::with(['itemProduksi.kategoriUsaha'])
            ->where('id_pengguna', $userId)
            ->whereNotNull('id_item_produksi')
            ->withCount(['pesan as unread_count' => function ($q) use ($userId) {
                $q->whereNull('dibaca_pada')
                    ->where('id_pengirim', '!=', $userId);
            }])
            ->orderByDesc('terakhir_aktif')
            ->get();

        return view('test.chat-klien', [
            'percakapanList' => $percakapanList,
            'userId' => $userId,
        ]);
    }

    public function messages(Request $request, $id)
    {
        $userId = Auth::id();

        $percakapan = Percakapan::where('id_percakapan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        $afterId = (int) $request->query('after_id', 0);

        $query = $percakapan->pesan()->orderBy('id_pesan');
        if ($afterId > 0) {
            $query->where('id_pesan', '>', $afterId);
        }

        $messages = $query->get();

        Pesan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_pengirim', '!=', $userId)
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        $lastId = $messages->last() ? $messages->last()->id_pesan : $afterId;

        return response()->json([
            'messages' => $messages->map(function ($m) {
                return [
                    'id' => $m->id_pesan,
                    'sender_id' => $m->id_pengirim,
                    'text' => $m->isi_pesan,
                    'created_at' => $m->created_at->format('Y-m-d H:i'),
                ];
            }),
            'last_id' => $lastId,
        ]);
    }

    public function send(Request $request, $id)
    {
        $userId = Auth::id();

        $data = $request->validate([
            'isi_pesan' => ['required', 'string', 'max:2000'],
        ]);

        $percakapan = Percakapan::where('id_percakapan', $id)
            ->where('id_pengguna', $userId)
            ->firstOrFail();

        $pesan = Pesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'id_pengirim' => $userId,
            'isi_pesan' => $data['isi_pesan'],
        ]);

        $percakapan->update(['terakhir_aktif' => now()]);

        return response()->json(['ok' => true, 'id' => $pesan->id_pesan]);
    }

    public function unreadCount()
    {
        $userId = Auth::id();

        $count = Pesan::whereNull('dibaca_pada')
            ->where('id_pengirim', '!=', $userId)
            ->whereHas('percakapan', function ($q) use ($userId) {
                $q->where('id_pengguna', $userId);
            })
            ->count();

        return response()->json(['count' => $count]);
    }
}
