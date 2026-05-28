<div class="modal fade" id="hapusModal{{ $item->id_kategori }}" tabindex="-1" role="dialog"
    aria-labelledby="hapusModalLabel{{ $item->id_kategori }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="hapusModalLabel{{ $item->id_kategori }}">
                    Hapus {{ $title }}?
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>

            <div class="modal-body text-left" style="font-weight: bold; font-size: 14px;">
                <div class="row">
                    <div class="col-6">Nama Kategori</div>
                    <div class="col-6">: {{ $item->nama_kategori }}</div>
                </div>

                <div class="row">
                    <div class="col-6">Kode Unik</div>
                    <div class="col-6">: {{ $item->kode_unik }}</div>
                </div>

                <div class="row">
                    <div class="col-6">Jenis Harga</div>
                    <div class="col-6">: {{ $item->jenis_harga }}</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Tutup
                </button>

                <form action="{{ route('kategoriUsaha.destroy', $item->id_kategori) }}" method="POST">
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