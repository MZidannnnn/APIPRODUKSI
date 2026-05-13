<div class="modal fade" id="hapusModal{{ $item->id_status_pesanan }}" tabindex="-1" role="dialog"
    aria-labelledby="hapusModalLabel{{ $item->id_status_pesanan }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="hapusModalLabel{{ $item->id_status_pesanan }}">
                    Hapus {{ $title }}?
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>

            <div class="modal-body text-left" style="font-weight: bold; font-size: 14px;">
                <div class="row">
                    <div class="col-6">Nama Status Pesanan</div>
                    <div class="col-6">: {{ $item->nama_status_pesanan }}</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>

                <form action="{{ route('statusPesanan.destroy', $item->id_status_pesanan) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>