<div class="modal fade" id="Modalbody" tabindex="-1" aria-labelledby="Modal-title" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" id="form-data">
                    <input type="hidden" id="id_masuk" name="id_masuk" />

                    <!-- Section: Data Supplier -->
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-truck me-2"></i>Data Supplier
                        </h6>
                    </div>

                    <div class="col-md-6">
                        <label for="nama_customer" class="form-label">
                            Nama Supplier <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer"
                            placeholder="Masukkan nama supplier">
                    </div>

                    <div class="col-md-6">
                        <label for="no_telfon" class="form-label">
                            Telepon <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="no_telfon" name="no_telfon"
                            placeholder="Masukkan no. telepon">
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="Masukkan email supplier">
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_masuk" class="form-label">
                            Tanggal Masuk <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk"
                            value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-12">
                        <label for="alamat" class="form-label">
                            Alamat Supplier <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"
                            placeholder="Masukkan alamat lengkap supplier"></textarea>
                    </div>

                    <!-- Section: Data Barang -->
                    <div class="col-12 mt-4">
                        <h6 class="border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-box me-2"></i>Data Barang
                        </h6>
                    </div>

                    <div class="col-md-12">
                        <label for="id_barang" class="form-label">
                            Nama Barang <span class="text-danger">*</span>
                        </label>
                        <select name="id_barang" class="form-select" id="id_barang">
                            <option value="">-- Pilih Nama Barang --</option>
                        </select>
                    </div>

                    <!-- Section: Detail Transaksi -->
                    <div class="col-12 mt-4">
                        <h6 class="border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-clipboard-list me-2"></i>Detail Transaksi
                        </h6>
                    </div>

                    <!-- Informasi Tabung -->
                    <div class="col-12 mt-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="jumlah_isi" class="form-label">
                                    Tabung Isi <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="jumlah_isi" name="jumlah_isi"
                                    placeholder="0" min="0" value="0">
                            </div>

                            <div class="col-md-4">
                                <label for="jumlah_kosong" class="form-label">
                                    Tabung Kosong <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="jumlah_kosong" name="jumlah_kosong"
                                    placeholder="0" min="0" value="0">
                            </div>

                            <div class="col-md-4">
                                <label for="keterangan" class="form-label">
                                    Keterangan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan"
                                    placeholder="Masukkan keterangan">
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah Item -->
                    <div class="col-12 mt-3">
                        <button type="button" class="btn btn-primary btn-md" id="btnTambahItem">
                            <i class="fas fa-plus me-2"></i>Tambah Item
                        </button>
                    </div>

                    <!-- Tabel Item -->
                    <div class="col-12 mt-4">
                        <h6 class="border-bottom pb-2 mb-3" style="color: #FF6C0C">
                            <i class="fas fa-list me-2"></i>Daftar Item Transaksi Masuk
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th style="width: 15%;">Nama Barang</th>
                                        <th style="width: 20%;">Nama Supplier</th>
                                        <th style="width: 15%;">Email</th>
                                        <th style="width: 12%;">No. Telepon</th>
                                        <th style="width: 10%;">Tabung Isi</th>
                                        <th style="width: 10%;">Tabung Kosong</th>
                                        <th style="width: 10%;">Tanggal Masuk</th>
                                        <th style="width: 8%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableItemList">
                                    <tr>
                                        <td colspan="8" class="text-center">-- Belum ada item ditambahkan --</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0);" class="btn btn-link link-dark fw-medium btn-sm"
                    data-bs-dismiss="modal">
                    <i class="ri-close-line me-1 align-middle"></i> Close
                </a>
                <button type="button" class="btn btn-sm" id="btn-save"
                    style="background:#F07124; color: aliceblue;">
                    <i class="fas fa-save me-1"></i>Simpan Transaksi
                </button>
            </div>
        </div>
    </div>
</div>
