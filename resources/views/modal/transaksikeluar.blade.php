<div class="modal fade" id="Modalbody" tabindex="-1" aria-labelledby="Modal-title" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" id="form-data">
                    <input type="hidden" id="id_keluar">

                    <!-- Section: Data Pelanggan -->
                    <div class="col-12">
                        <h6 class=" border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-user me-2"></i>Data Pelanggan
                        </h6>
                    </div>

                    <div class="col-md-3">
                        <label for="kode_customer" class="form-label">Kode Pelanggan</label>
                        <input type="text" class="form-control" id="kode_customer" placeholder="Auto Generate"
                            disabled readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="id_customer" class="form-label">
                            Nama Pelanggan <span class="text-danger">*</span>
                        </label>
                        <select name="id_customer" class="form-select" id="id_customer" required>
                            <option value="">-- Pilih Nama Pelanggan --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="tanggal_keluar" class="form-label">Tanggal Keluar</label>
                        <input type="text" class="form-control" id="tanggal_keluar" value="{{ date('d/m/Y') }}"
                            disabled readonly>
                    </div>

                    <!-- Section: Data Barang -->
                    <div class="col-12 mt-4">
                        <h6 class=" border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-box me-2"></i>Data Barang
                        </h6>
                    </div>

                    <div class="col-md-3">
                        <label for="kode_barang" class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" placeholder="Auto Generate" disabled
                            readonly>
                    </div>

                    <div class="col-md-6">
                        <label for="id_barang" class="form-label">
                            Nama Barang <span class="text-danger">*</span>
                        </label>
                        <select name="id_barang" class="form-select" id="id_barang" required>
                            <option value="">-- Pilih Nama Barang --</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="harga_satuan" class="form-label">Harga Satuan</label>
                        <input type="text" class="form-control" id="harga_satuan" placeholder="Rp 0" disabled
                            readonly>
                    </div>

                    <!-- Section: Detail Transaksi -->
                    <div class="col-12 mt-4">
                        <h6 class=" border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-clipboard-list me-2"></i>Detail Transaksi
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <label for="nama_pengirim" class="form-label">
                            Nama Pengirim <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama_pengirim"
                            placeholder="Masukkan nama pengirim" required>
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">
                            Metode Pembayaran <span class="text-danger">*</span>
                        </label>
                        <select name="status" class="form-select" id="status" required>
                            <option value="">-- Pilih Pembayaran --</option>
                            <option value="cash">Cash</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" placeholder="Keterangan (opsional)">
                    </div>

                    <!-- Informasi Tabung -->
                    <div class="col-12 mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="jumlah_isi" class="form-label">
                                    Tabung Isi <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="jumlah_isi" placeholder="0"
                                    min="0" value="0" required>
                            </div>

                            <div class="col-md-4">
                                <label for="jumlah_kosong" class="form-label">
                                    Tabung Kosong <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="jumlah_kosong" placeholder="0"
                                    min="0" value="0" required>
                            </div>

                            <div class="col-md-4">
                                <label for="pinjam_tabung" class="form-label">
                                    Tabung Pinjam <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="pinjam_tabung" placeholder="0"
                                    min="0" value="0" required>
                            </div>
                        </div>
                    </div>


                    <!-- Tombol Tambah Item -->
                    <div class="col-12 mt-2">
                        <button type="button" class="btn btn-primary btn-md" id="btnTambahItem">
                            <i class="fas fa-plus me-2"></i>Tambah Item
                        </button>
                    </div>

                    <!-- Tabel Item -->
                    <div class="col-12 mt-4">
                        <h6 class=" border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-list me-2"></i>Daftar Item Transaksi
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th width="12%">Nama Barang</th>
                                        <th width="12%">Nama Customer</th>
                                        <th width="10%">Nama Pengirim</th>
                                        <th width="7%">Tabung Isi</th>
                                        <th width="7%">Tabung Kosong</th>
                                        <th width="7%">Tabung Pinjam</th>
                                        <th width="10%">Harga Satuan</th>
                                        <th width="10%">Total Harga</th>
                                        <th width="12%">Keterangan</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableItemList">
                                    <tr>
                                        <td colspan="11" class="text-center">-- Belum ada item ditambahkan --</td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="8" class="text-end fw-bold">Grand Total:</td>
                                        <td colspan="3" class="fw-bold " id="grandTotal">Rp 0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
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
