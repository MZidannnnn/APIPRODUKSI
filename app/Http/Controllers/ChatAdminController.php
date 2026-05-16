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
        $percakapanList = Percakapan::with('pengguna')
            ->orderByDesc('terakhir_aktif')
            ->get();

        return view('test.list-chat-admin', [
            'percakapanList' => $percakapanList,
        ]);
    }

    public function show($id)
    {
        $percakapan = Percakapan::with('pengguna')->findOrFail($id);

        return view('test.detail-chat-admin', [
            'percakapan' => $percakapan,
            'userId' => Auth::id(),
        ]);
    }

    public function messages(Request $request, $id)
    {
        $afterId = (int) $request->query('after_id', 0);

        $percakapan = Percakapan::findOrFail($id);

        $query = $percakapan->pesan()
            ->with('pengirim:id_pengguna,nama_pengguna')
            ->orderBy('id_pesan');

        if ($afterId > 0) {
            $query->where('id_pesan', '>', $afterId);
        }

        $messages = $query->get();

        Pesan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_pengirim', '!=', Auth::id())
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        $lastId = $messages->last() ? $messages->last()->id_pesan : $afterId;

        return response()->json([
            'messages' => $messages->map(function ($m) {
                return [
                    'id' => $m->id_pesan,
                    'sender_id' => $m->id_pengirim,
                    'sender_name' => $m->pengirim?->nama_pengguna ?? 'User',
                    'text' => $m->isi_pesan,
                    'created_at' => $m->created_at->format('Y-m-d H:i'),
                ];
            }),
            'last_id' => $lastId,
        ]);
    }

    public function send(Request $request, $id)
    {
        $data = $request->validate([
            'isi_pesan' => ['required', 'string', 'max:2000'],
        ]);

        $percakapan = Percakapan::findOrFail($id);

        $pesan = Pesan::create([
            'id_percakapan' => $percakapan->id_percakapan,
            'id_pengirim' => Auth::id(),
            'isi_pesan' => $data['isi_pesan'],
        ]);

        $percakapan->update(['terakhir_aktif' => now()]);

        return response()->json([
            'ok' => true,
            'id' => $pesan->id_pesan,
        ]);
    }
}
