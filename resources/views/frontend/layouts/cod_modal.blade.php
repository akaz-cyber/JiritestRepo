<div class="modal fade" id="codModal" tabindex="-1" role="dialog" aria-labelledby="codModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="codModalLabel">
                    <i class="fas fa-store mr-2"></i>Informasi Ambil di Tempat
                </h5>
                <button type="button" class="close text-white modal-close-btn-cod" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Pesanan Berhasil Dibuat!</h4>
                    <p class="text-muted">Silakan lakukan konfirmasi kedatangan untuk pengambilan barang.</p>
                </div>

                <div class="card bg-light mb-3">
                    <div class="card-body py-2">
                        <div class="row">
                            <div class="col-6 text-muted">No. Pesanan:</div>
                            <div class="col-6 font-weight-bold text-right" id="cod-order-number">...</div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-6 text-muted">Total Bayar:</div>
                            <div class="col-6 font-weight-bold text-right text-danger" id="cod-total-amount">...</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-sticky alert-info" role="alert" style="font-size: 0.9rem;">
                    <strong>Lokasi Jirifarm:</strong><br>
                    Silakan hubungi kami via WhatsApp untuk share location dan janji temu pengambilan.
                </div>

                {{-- Tombol WhatsApp --}}
                <a href="#" id="cod-wa-link" target="_blank" class="btn btn-success btn-block btn-lg">
                    <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
                </a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn-cod" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
