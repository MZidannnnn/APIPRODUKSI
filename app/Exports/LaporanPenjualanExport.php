<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPenjualanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnFormatting, WithEvents, ShouldAutoSize
{
    public function __construct(
        private string $tanggalMulai,
        private string $tanggalSelesai
    ) {}

    public function query()
    {
        return DB::table('pembayaran as pb')
            ->join('pesanan as ps', 'pb.id_pesanan', '=', 'ps.id_pesanan')
            ->leftJoin('rincian_pesanan as rp', 'ps.id_pesanan', '=', 'rp.id_pesanan')
            ->join('detail_produk as dp', 'ps.id_detail_produk', '=', 'dp.id_detail_produk')
            ->join('item_produksi as ip', 'dp.id_item_produksi', '=', 'ip.id_item_produksi')
            ->join('status_pesanan as sp', 'ps.id_status_pesanan', '=', 'sp.id_status_pesanan')
            ->select([
                'ps.id_pesanan',
                'ps.tanggal_pesan',
                'ps.nama_penerima',
                'ip.nama_item',
                'dp.ukuran',
                DB::raw('COALESCE(rp.kuantitas, 1) as kuantitas'),
                'ps.total_harga',
                'sp.nama_status_pesanan as status_pesanan',
                'pb.created_at as tanggal_bayar',
                'pb.tipe_pembayaran',
                'pb.jumlah_bayar',
                'pb.payment_type',
                'pb.status_bayar',
            ])
            ->whereBetween('ps.tanggal_pesan', [$this->tanggalMulai, $this->tanggalSelesai])
            ->orderBy('ps.tanggal_pesan')
            ->orderBy('ps.id_pesanan')
            ->orderBy('pb.created_at');
    }

    public function headings(): array
    {
        return [
            'No Pesanan',
            'Tgl Pesan',
            'Nama Penerima',
            'Produk',
            'Ukuran',
            'Quantity',
            'Total Harga',
            'Status Pesanan',
            'Tgl Bayar',
            'Tipe Bayar',
            'Jumlah Bayar',
            'Payment Type',
            'Status Bayar',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_pesanan,
            Date::stringToExcel($row->tanggal_pesan),
            $row->nama_penerima,
            $row->nama_item,
            $row->ukuran,
            $row->kuantitas,
            $row->total_harga,
            $row->status_pesanan,
            $row->tanggal_bayar ? Date::dateTimeToExcel(Carbon::parse($row->tanggal_bayar)) : null,
            $row->tipe_pembayaran,
            $row->jumlah_bayar,
            $row->payment_type,
            $row->status_bayar,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFEFEFEF'],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'G' => '#,##0',
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'K' => '#,##0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $range = "A1:{$lastColumn}{$lastRow}";

                $sheet->setAutoFilter("A1:{$lastColumn}1");
                $sheet->freezePane('A2');

                $sheet->getStyle($range)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("F2:F{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("G2:G{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("K2:K{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}