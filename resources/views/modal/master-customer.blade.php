    <div class="modal fade" id="ModalCustomer" tabindex="-1" aria-labelledby="ModalTitleCustomer" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalTitleCustomer"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" id="form-data">
                        <input type="hidden" id="id_customer">

                        <div class="col-md-3">
                            <label for="kode_customer" class="form-label">Kode Pelanggan</label>
                            <input type="text" class="form-control" id="kode_customer"
                                placeholder="Generate by system" disabled readonly>
                        </div>

                        <div class="col-md-6">
                            <label for="nama_customer" class="form-label">Nama </label>
                            <input type="text" class="form-control" id="nama_customer" placeholder="Nama ">
                        </div>
                        <div class="col-md-6">
                            <label for="" class="form-label">Telepon</label>
                            <input type="text" class="form-control" id="telepon" placeholder="Telepon ">
                        </div>
                        <div class="col-md-6">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" placeholder="Alamat Customer">
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
