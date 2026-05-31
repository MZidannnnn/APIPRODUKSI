<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Percakapan;
use App\Models\Pesan;
use App\Models\PesanLampiran;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

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
        return view('admin/chat-admin/chat', compact('percakapanList'));
    }

    public function show(Percakapan $percakapan)
    {
        $admin = Auth::user();

        $percakapan = Percakapan::where('id_percakapan', $percakapan->id_percakapan)
            ->where('id_kategori', $admin->id_kategori)
            ->firstOrFail();

        return view('admin/chat-admin/detail-chat', [
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
            ->with([
                'pengirim:id_pengguna,nama_pengguna',
                'lampiran'
            ])
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
                'attachments' => $m->lampiran->map(fn($a) => $this->formatAttachment($a))->values(),
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

    public function send(StoreChatMessageRequest $request, Percakapan $percakapan)
    {
        Gate::authorize('accessAdmin', $percakapan);
        $admin = Auth::user();

        // if ($percakapan->id_kategori !== $admin->id_kategori) {
        //     abort(403, 'Anda tidak memiliki otorisasi untuk percakapan ini.');
        // }
        // $percakapan = Percakapan::where('id_percakapan', $percakapan->id_percakapan)
        //     ->where('id_kategori', $admin->id_kategori)
        //     ->firstOrFail();

        $data = $request->validated();

        $pesan = DB::transaction(function () use ($request, $percakapan, $admin, $data) {
            $pesan = Pesan::create([
                'id_percakapan' => $percakapan->id_percakapan,
                'id_pengirim' => $admin->id_pengguna,
                'isi_pesan' => $data['isi_pesan'] ?? null,
            ]);

            if ($request->hasFile('lampiran')) {
                foreach ($request->file('lampiran', []) as $file) {
                    $this->storeAttachment($file, $pesan, $percakapan->id_percakapan);
                }
            }

            $percakapan->update(['terakhir_aktif' => now()]);

            return $pesan;
        });

        return response()->json(['ok' => true, 'id' => $pesan->id_pesan]);
    }

    private function formatAttachment(PesanLampiran $a): array
    {
        return [
            'id' => $a->id_lampiran,
            'type' => $a->jenis,
            'name' => $a->original_name,
            'size' => $a->size_bytes,
            'mime' => $a->mime_type,
            'preview_url' => $a->jenis === 'image'
                ? route('chat.attachments.preview', $a->id_lampiran)
                : null,
            'download_url' => route('chat.attachments.download', $a->id_lampiran),
        ];
    }

    private function storeAttachment(UploadedFile $file, Pesan $pesan, int $percakapanId): void
    {
        $mimeMap = [
            'image/jpeg' => ['type' => 'image', 'ext' => 'jpg'],
            'image/png' => ['type' => 'image', 'ext' => 'png'],
            'image/gif' => ['type' => 'image', 'ext' => 'gif'],
            'image/webp' => ['type' => 'image', 'ext' => 'webp'],
            'application/pdf' => ['type' => 'design', 'ext' => 'pdf'],
            'application/zip' => ['type' => 'design', 'ext' => 'zip'],
            'application/x-rar-compressed' => ['type' => 'design', 'ext' => 'rar'],
            'application/vnd.rar' => ['type' => 'design', 'ext' => 'rar'],
            'image/vnd.adobe.photoshop' => ['type' => 'design', 'ext' => 'psd'],
            'application/postscript' => ['type' => 'design', 'ext' => 'eps'],
            'application/vnd.adobe.illustrator' => ['type' => 'design', 'ext' => 'ai'],
        ];

        $mime = $file->getMimeType() ?? '';
        $meta = $mimeMap[$mime] ?? null;

        if (! $meta) {
            return;
        }

        $original = basename($file->getClientOriginalName());
        $base = pathinfo($original, PATHINFO_FILENAME);
        $safeBase = Str::slug(Str::ascii($base)) ?: 'file';
        // $storedName = $safeBase . '-' . Str::random(10) . '.' . $meta['ext'];

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'lampiran' => 'Upload gagal. Silakan ulangi.'
            ]);
        }

        $dir = 'percakapan/' . $percakapanId;
        $storedName = $safeBase . '-' . Str::random(10) . '.' . $meta['ext'];

        if ($dir === '' || $storedName === '') {
            throw new RuntimeException('Path penyimpanan tidak valid.');
        }

        $path = $file->storeAs($dir, $storedName, 'chat_private');

        if (! $path) {
            throw new RuntimeException('Gagal menyimpan file.');
        }

        $width = null;
        $height = null;
        if ($meta['type'] === 'image') {
            $size = @getimagesize($file->getPathname());
            if (is_array($size)) {
                $width = $size[0];
                $height = $size[1];
            }
        }

        PesanLampiran::create([
            'id_pesan' => $pesan->id_pesan,
            'jenis' => $meta['type'],
            'disk' => 'chat_private',
            'path' => $path,
            'original_name' => $original,
            'stored_name' => $storedName,
            'mime_type' => $mime,
            'size_bytes' => $file->getSize() ?: 0,
            'width' => $width,
            'height' => $height,
            'checksum' => hash_file('sha256', $file->getPathname()) ?: '',
        ]);
    }
}
