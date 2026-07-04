<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPenjualanExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
    // Format angka Rupiah standar Excel: Rp1.234.567
    private const RUPIAH_FORMAT = '"Rp"#,##0;[RED]-"Rp"#,##0';

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
                'ps.kode_resi_pesanan',
                'ps.tanggal_pesan',
                'ps.updated_at as tanggal_selesai',
                'ps.nama_penerima',
                'ip.nama_item',
                'dp.ukuran',
                DB::raw('COALESCE(rp.kuantitas, 1) as kuantitas'),
                'ps.total_harga',
                'sp.nama_status_pesanan as status_pesanan',
                'pb.tipe_pembayaran',
                'pb.jumlah_bayar',
                'pb.payment_type',
            ])
            ->whereBetween('ps.tanggal_pesan', [
                $this->tanggalMulai,
                $this->tanggalSelesai
            ])
            ->where('pb.status_bayar', 'Lunas')
            ->where('sp.nama_status_pesanan', 'Pesanan Selesai')
            ->orderByDesc('ps.tanggal_pesan')
            ->orderByDesc('ps.id_pesanan')
            ->orderByDesc('pb.created_at');
    }

    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Tgl Pesan',
            'Tgl Selesai',
            'Nama Pemesan',
            'Produk',
            'Ukuran',
            'Quantity',
            'Total Harga',
            'Tipe Bayar',
            'Jumlah Bayar',
            'Payment Type',
        ];
    }

    public function map($row): array
    {
        return [
            $row->kode_resi_pesanan ?? '#' . $row->id_pesanan,
            $row->tanggal_pesan ? Date::stringToExcel($row->tanggal_pesan) : null,
            $row->tanggal_selesai ? Date::stringToExcel($row->tanggal_selesai) : null,
            $row->nama_penerima,
            $row->nama_item,
            $row->ukuran,
            $row->kuantitas,
            $row->total_harga,
            $row->tipe_pembayaran,
            $row->jumlah_bayar,
            $row->payment_type,
        ];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Baris header tabel (baris ke-3)
            3 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2E5395'], // biru gelap
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF2E5395'],
                    ],
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'G' => '#,##0',
            'H' => self::RUPIAH_FORMAT,
            'J' => self::RUPIAH_FORMAT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastColumn = $sheet->getHighestColumn();
                $firstDataRow = 4;

                // Hitung total penjualan
                $totalPenjualan = DB::table('pembayaran as pb')
                    ->join('pesanan as ps', 'pb.id_pesanan', '=', 'ps.id_pesanan')
                    ->join('status_pesanan as sp', 'ps.id_status_pesanan', '=', 'sp.id_status_pesanan')
                    ->whereBetween('ps.tanggal_pesan', [
                        $this->tanggalMulai,
                        $this->tanggalSelesai
                    ])
                    ->where('pb.status_bayar', 'Lunas')
                    ->where('sp.nama_status_pesanan', 'Pesanan Selesai')
                    ->sum('pb.jumlah_bayar');

                // ===== Title laporan =====
                $sheet->setCellValue('A1', 'LAPORAN PENJUALAN');
                $sheet->setCellValue('A2', "Periode: {$this->tanggalMulai} s/d {$this->tanggalSelesai}");

                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->mergeCells("A2:{$lastColumn}2");

                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(22);

                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FF2E5395'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 11,
                        'color' => ['argb' => 'FF595959'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // ===== Autofilter & freeze header =====
                $sheet->setAutoFilter("A3:{$lastColumn}3");
                $sheet->freezePane('A4');

                // ===== Border seluruh area data =====
                $sheet->getStyle("A3:{$lastColumn}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD9D9D9'],
                        ],
                    ],
                ]);

                // ===== Zebra striping baris data =====
                for ($row = $firstDataRow; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFF7F9FC'],
                            ],
                        ]);
                    }
                }

                // ===== Alignment data =====
                $sheet->getStyle("A{$firstDataRow}:A{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("F{$firstDataRow}:F{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("G{$firstDataRow}:G{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("H{$firstDataRow}:H{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("J{$firstDataRow}:J{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("I{$firstDataRow}:I{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("K{$firstDataRow}:K{$lastRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ===== Total row =====
                $totalRow = $lastRow + 2;

                $sheet->setCellValue("J{$totalRow}", 'TOTAL PENJUALAN');
                $sheet->setCellValue("K{$totalRow}", $totalPenjualan);

                $sheet->getStyle("J{$totalRow}:K{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['argb' => 'FF1B5E20'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFDFF0D8'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF1B5E20'],
                        ],
                    ],
                ]);

                $sheet->getStyle("J{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getStyle("K{$totalRow}")
                    ->getNumberFormat()
                    ->setFormatCode(self::RUPIAH_FORMAT);

                $sheet->getStyle("K{$totalRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                $sheet->getRowDimension($totalRow)->setRowHeight(24);
            },
        ];
    }
}