    <div class="modal fade" id="Modalbody" tabindex="-1" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalTitle">Form Stok Opname</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="form-data">
                        <div class="col-md-12">
                            <label for="id_barang" class="form-label">Pilih Barang <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="id_barang" name="id_barang" required>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                        </div>


                        <div class="col-md-4">
                            <label for="nama_barang" class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" placeholder="Nama Barang"
                                disabled readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="text" class="form-control" id="kapasitas" placeholder="Kapasitas" disabled
                                readonly>
                        </div>
                        <div class="col-md-12">
                            <label for="tanggal_opname" class="form-label">Tanggal Opname <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_opname" required>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted mb-3">Stok Sistem</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="stok_sistem_isi" class="form-label">Stok Sistem (Isi)</label>
                            <input type="number" class="form-control" id="stok_sistem_isi" placeholder="0" disabled
                                readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="stok_sistem_kosong" class="form-label">Stok Sistem (Kosong)</label>
                            <input type="number" class="form-control" id="stok_sistem_kosong" placeholder="0" disabled
                                readonly>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted mb-3">Stok Fisik (Input Hasil Opname)</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="stok_isi_fisik" class="form-label">Stok Fisik (Isi) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok_isi_fisik"
                                placeholder="Masukkan jumlah stok isi" min="0" required>
                            <small class="text-muted">Selisih: <span id="preview-selisih-isi">-</span></small>
                        </div>
                        <div class="col-md-6">
                            <label for="stok_kosong_fisik" class="form-label">Stok Fisik (Kosong) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok_kosong_fisik"
                                placeholder="Masukkan jumlah stok kosong" min="0" required>
                            <small class="text-muted">Selisih: <span id="preview-selisih-kosong">-</span></small>
                        </div>

                        <div class="col-12">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="2" placeholder="Catatan hasil opname (opsional)"></textarea>
                        </div>

                        <div class="modal-footer">
                            <a href="javascript:void(0);" class="btn btn-link link-dark fw-medium btn-sm"
                                data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                            <button type="button" class="btn  btn-sm" id="btn-save"
                                style="background:#F07124; color: aliceblue; ">
                                <i class="fas fa-save me-1"></i>Simpan Koreksi Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
