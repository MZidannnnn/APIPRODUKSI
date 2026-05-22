<?php

namespace App\Http\Controllers;

use App\Models\PesanLampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatAttachmentController extends Controller
{
    //
    public function preview(PesanLampiran $lampiran)
    {
        Gate::authorize('view', $lampiran);

        $disk = Storage::disk($lampiran->disk);
        if (! $disk->exists($lampiran->path)) {
            abort(404);
        }

        $path = $disk->path($lampiran->path);

        return response()->file($path, [
            'Content-Type' => $lampiran->mime_type,
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.$lampiran->stored_name.'"',
        ]);
    }

    public function download(PesanLampiran $lampiran)
    {
        Gate::authorize('view', $lampiran);

        $disk = Storage::disk($lampiran->disk);
        if (! $disk->exists($lampiran->path)) {
            abort(404);
        }

        $path = $disk->path($lampiran->path);
        $downloadName = $this->sanitizeDownloadName($lampiran->original_name ?: $lampiran->stored_name);

        return response()->download($path, $downloadName, [
            'Content-Type' => $lampiran->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function sanitizeDownloadName(string $name): string
    {
        $name = Str::of($name)->replace(["\r", "\n", '"'], '')->__toString();
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name) ?: 'lampiran';
        return $name;
    }
}
