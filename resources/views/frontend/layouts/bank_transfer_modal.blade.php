<div class="modal fade" id="bankTransferModal" tabindex="-1" role="dialog" aria-labelledby="bankTransferModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bankTransferModalLabel">Instruksi Pembayaran Bank Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Pesanan Anda telah kami terima. Silakan selesaikan pembayaran ke salah satu rekening berikut:</p>

                <div class="bank-account mb-3 p-3 text-center"
                    style="background-color: #fef8e5; border: 1px solid #fde08a;">
                    <h5 class="modal-title-custom" style="margin-bottom: 5px; font-size: 1.1rem; font-weight: 500;">
                        Total
                        Pembayaran:</h5>
                    <h3 id="modal-total-amount"
                        style="font-weight: 700; color: #d9534f; margin-bottom: 0; font-size: 2rem;">
                        Rp...
                    </h3>
                </div>
                {{-- GANTI DETAIL BANK DI BAWAH INI --}}
                <div class="bank-account mb-3 p-3" style="background-color: #f8f9fa; border-radius: 5px;">
                    <strong>MANDIRI</strong><br>
                    No. Rekening: <strong>11900-06472-870</strong><br>
                    Atas Nama: <strong>Curug Lestarimaju</strong>
                </div>

                <div class="bank-account mb-3 p-3" style="background-color: #f8f9fa; border-radius: 5px;">
                    <strong>Bank BCA</strong><br>
                    No. Rekening: <strong>00230-25253</strong><br>
                    Atas Nama: <strong>CURUG LESTARI MAJU PT</strong>
                </div>

                <hr>

                <p><strong>PENTING:</strong> Setelah melakukan transfer, mohon segera lakukan konfirmasi pembayaran
                    dengan menekan tombol di bawah ini.</p>
                <a href="https://wa.me/628988199366?text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20pesanan%20..."
                    id="modal-user-wa-link" {{-- <-- TAMBAHKAN ID INI --}} target="_blank" class="btn btn-success btn-block"
                    style="background-color: #25D366; border-color: #25D366; color: white;">
                    <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
                </a>

                <small class="form-text text-muted mt-2 text-center">
                    Anda dapat menemukan kembali info pembayaran ini di halaman "Daftar Pesanan" Anda.
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
