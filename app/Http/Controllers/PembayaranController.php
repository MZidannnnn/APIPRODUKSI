<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTransactionRequest;
use App\Http\Requests\MidtransNotificationRequest;
use App\Http\Requests\UploadBuktiPembayaranRequest;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use App\Services\MidtransSnapService;
use App\Services\PembayaranStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class PembayaranController extends Controller
{

    public function createTransaction(CreateTransactionRequest $request, MidtransSnapService $midtrans)
    {
        $pesanan = Pesanan::query()
            ->with(['statusPesanan', 'rincianPesanan'])
            ->where('id_pesanan', $request->id_pesanan)
            ->where('id_pengguna', $request->user()->id_pengguna ?? Auth::id())
            ->firstOrFail();

        $status = $pesanan->statusPesanan->nama_status_pesanan ?? null;

        if ($request->tipe_pembayaran === 'DP') {
            abort_unless($status === 'Menunggu Pembayaran', 422, 'Status pesanan tidak valid.');
            $dpPaid = $pesanan->pembayaran()
                ->where('tipe_pembayaran', 'DP')
                ->where('status_bayar', 'Lunas')
                ->exists();
            abort_if($dpPaid, 422, 'DP sudah dibayar.');
        }

        if ($request->tipe_pembayaran === 'Pelunasan') {
            abort_unless($status === 'Selesai', 422, 'Pesanan belum selesai.');
            abort_if($pesanan->sisaBayar() <= 0, 422, 'Tagihan sudah lunas.');

            $existingPending = $pesanan->pembayaran()
                ->where('tipe_pembayaran', 'Pelunasan')
                ->where('status_bayar', 'Pending')
                ->latest('id_pembayaran')
                ->first();

            if ($existingPending) {
                $expiresAt = $existingPending->snap_expires_at
                    ?? $existingPending->created_at?->copy()->addHours(24);

                if ($expiresAt && $expiresAt->isFuture()) {
                    return response()->json([
                        'snap_token' => $existingPending->snap_token,
                        'id_pembayaran' => $existingPending->id_pembayaran,
                    ]);
                }
            }
        }
        try {
            $snap = $midtrans->createSnapToken($pesanan, $request->tipe_pembayaran);

            $existingPending = null;

            if ($request->tipe_pembayaran === 'Pelunasan') {
                $existingPending = $pesanan->pembayaran()
                    ->where('tipe_pembayaran', 'Pelunasan')
                    ->where('status_bayar', 'Pending')
                    ->latest('id_pembayaran')
                    ->first();
            }

            if ($existingPending) {
                $existingPending->update([
                    'jumlah_bayar' => $snap['gross_amount'],
                    'order_id' => $snap['order_id'],
                    'snap_token' => $snap['snap_token'],
                    'snap_expires_at' => now()->addHours(24),
                ]);

                $pembayaran = $existingPending;
            } else {
                $pembayaran = Pembayaran::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'tipe_pembayaran' => $request->tipe_pembayaran,
                    'jumlah_bayar' => $snap['gross_amount'],
                    'order_id' => $snap['order_id'],
                    'snap_token' => $snap['snap_token'],
                    'snap_expires_at' => now()->addHours(24),
                    'status_bayar' => 'Pending',
                ]);
            }

            return response()->json([
                'snap_token' => $snap['snap_token'],
                'id_pembayaran' => $pembayaran->id_pembayaran,
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Gagal membuat transaksi.'], 500);
        }
    }

    public function notification(MidtransNotificationRequest $request, PembayaranStatusService $service)
    {
        $service->handleMidtransNotification($request->validated());

        return response()->json(['message' => 'OK']);
    }

    public function showUploadForm(Pembayaran $pembayaran)
    {
        $pembayaran->loadMissing('pesanan');

        abort_unless(
            (int) $pembayaran->pesanan?->id_pengguna === (int) Auth::id(),
            403,
            'Anda tidak berhak mengakses transaksi ini.'
        );

        return view('test.upload_bukti_pembayaran_klien', compact('pembayaran'));
    }

    // proses upload
    public function uploadBukti(UploadBuktiPembayaranRequest $request, Pembayaran $pembayaran)
    {
        $file = $request->file('bukti_bayar');

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = Str::slug($baseName);
        if ($safeBase === '') {
            $safeBase = 'bukti-bayar';
        }

        $ext = strtolower($file->guessExtension() ?: $file->extension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'], true)) {
            return back()->withErrors(['bukti_bayar' => 'Ekstensi file tidak valid.'])->withInput();
        }

        $fileName = $safeBase
            . '-'
            . $pembayaran->id_pembayaran
            . '-'
            . now()->format('YmdHis')
            . '-'
            . Str::lower(Str::random(10))
            . '.'
            . $ext;

        $directory = 'bukti-bayar/' . Auth::id() . '/' . now()->format('Y/m');
        $newPath = Storage::disk('local')->putFileAs($directory, $file, $fileName);

        if ($pembayaran->bukti_bayar) {
            Storage::disk('local')->delete($pembayaran->bukti_bayar);
        }

        $pembayaran->update([
            'bukti_bayar' => $newPath,
        ]);

        return redirect()
            ->route('pembayaran.upload.form', $pembayaran->id_pembayaran)
            ->with('success', 'Bukti bayar berhasil diunggah.');
    }

    public function TampilRiwayatTransaksi()
    {
        // $admin = Auth::user()->id_kategori;
        $user = Auth::user();
        $isSuperAdmin = (int) $user->id_role === 1;

        $riwayatTransaksi = Pembayaran::query()
            ->with([
                'pesanan:id_pesanan,id_detail_produk,nama_penerima',
                'pesanan.detailProduk:id_detail_produk,id_item_produksi',
                'pesanan.detailProduk.itemProduksi:id_item_produksi,id_kategori',
            ])
            ->when(! $isSuperAdmin, function ($query) use ($user) {
                $query->whereHas('pesanan.detailProduk.itemProduksi', function ($q) use ($user) {
                    $q->where('id_kategori', (int) $user->id_kategori);
                });
            })
            ->latest('id_pembayaran')
            ->get();

        $data = [
            'title' => 'Riwayat Transaksi Klien',
            'menuRiwayatTransaksi' => 'active',
            'riwayatTransaksi' => $riwayatTransaksi,
        ];

        return view('admin/riwayat-transaksi/index', $data);
    }

    public function retrySnap(Request $request)
    {
        $data = $request->validate([
            'id_pesanan' => ['required', 'integer', 'exists:pesanan,id_pesanan'],
            // 'tipe_pembayaran' => ['nullable', Rule::in(['DP', 'Full', 'Pelunasan'])],
        ]);

        $pesanan = Pesanan::query()
            ->with(['statusPesanan', 'latestPembayaran'])
            ->where('id_pesanan', $data['id_pesanan'])
            ->where('id_pengguna', $request->user()->id_pengguna ?? Auth::id())
            ->firstOrFail();

        if (($pesanan->statusPesanan->nama_status_pesanan ?? '') !== 'Menunggu Pembayaran') {
            return response()->json(['message' => 'Status pesanan tidak valid.'], 422);
        }

        $last = $pesanan->latestPembayaran;

        if (!$last) {
            return response()->json(['message' => 'Belum ada transaksi untuk pesanan ini.'], 404);
        }

        if ($last->status_bayar === 'Lunas') {
            return response()->json(['message' => 'Pesanan sudah lunas.'], 422);
        }
        $expiresAt = $last->snap_expires_at
            ?? $last->created_at?->copy()->addHours(24);

        if (
            $last->status_bayar === 'Pending'
            && $expiresAt
            && $expiresAt->isFuture()
        ) {
            return response()->json([
                'snap_token' => $last->snap_token,
                'id_pembayaran' => $last->id_pembayaran,
            ]);
        }

        $last->update(['status_bayar' => 'Kedaluwarsa']);

        $statusBatal = StatusPesanan::where('nama_status_pesanan', 'Dibatalkan')->first();
        if ($statusBatal) {
            $pesanan->update(['id_status_pesanan' => $statusBatal->id_status_pesanan]);
        }

        return response()->json(['message' => 'Token pembayaran sudah kedaluwarsa.'], 422);
    }

    public function cancelPesanan(Request $request, Pesanan $pesanan)
    {
        Gate::authorize('cancel', $pesanan);
        // $this->authorize('cancel', $pesanan);

        try {
            DB::transaction(function () use ($pesanan) {
                $pesanan->loadMissing(['statusPesanan', 'latestPembayaran']);

                $statusName = $pesanan->statusPesanan?->nama_status_pesanan;

                abort_unless(
                    in_array($statusName, ['Menunggu Pembayaran', 'Menunggu Diproses'], true),
                    422,
                    'Pesanan hanya bisa dibatalkan saat status Menunggu Pembayaran atau Menunggu Diproses.'
                );

                $latestPembayaran = $pesanan->latestPembayaran()->lockForUpdate()->first();

                if ($latestPembayaran && $latestPembayaran->status_bayar === 'Pending' && $latestPembayaran->order_id) {
                    try {
                        Transaction::cancel($latestPembayaran->order_id);
                    } catch (\Throwable $e) {
                        report($e);
                    }

                    $latestPembayaran->update([
                        'status_bayar' => 'Kedaluwarsa',
                    ]);
                }

                $statusBatalId = StatusPesanan::where('nama_status_pesanan', 'Dibatalkan')
                    ->value('id_status_pesanan');

                abort_unless($statusBatalId, 500, 'Status Dibatalkan belum tersedia.');

                $pesanan->update([
                    'id_status_pesanan' => $statusBatalId,
                ]);
            });

            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan.',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => $e->getMessage() ?: 'Gagal membatalkan pesanan.',
            ], 422);
        }
    }
}
