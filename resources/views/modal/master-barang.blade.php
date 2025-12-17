    <div class="modal fade" id="ModalBarang" tabindex="-1" aria-labelledby="ModalTitleBarang" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalTitleBarang"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="form-data">
                        <input type="hidden" id="id_barang" name="id_barang">


                        <div class="col-md-4">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" placeholder="Nama Barang">
                        </div>
                        <div class="col-md-4">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="text" class="form-control" id="kapasitas" placeholder="Kapasitas">
                        </div>
                        <div class="col-md-5">
                            <label for="harga_jual" class="form-label">Harga</label>
                            <input type="text" class="form-control" id="harga_jual" placeholder="Harga">
                        </div>
                        <div class="col-md-5">
                            <label for="stok_tabung_isi" class="form-label">Stok Tabung Isi</label>
                            <input type="text" class="form-control" id="stok_tabung_isi"
                                placeholder="Stok Tabung Isi">
                        </div>
                        <div class="col-md-5">
                            <label for="stok_tabung_kosong" class="form-label">Stok Tabung Kosong</label>
                            <input type="text" class="form-control" id="stok_tabung_kosong"
                                placeholder="Stok Tabung Kosong">
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0);" class="btn btn-link link-dark fw-medium btn-sm"
                                data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                            <button type="button" class="btn  btn-sm" id="btn-save"
                                style="background:#F07124; color: aliceblue; ">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
