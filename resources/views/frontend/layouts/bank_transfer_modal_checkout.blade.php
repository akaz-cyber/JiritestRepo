<div class="modal-overlay" id="bankTransferOverlay">
    <div class="modal-box" id="bankTransferBox">
        <div class="modal-header-custom">
            <h5 class="modal-title-custom">Instruksi Pembayaran Bank Transfer</h5>
            {{-- Tombol close di header --}}
            <button type="button" class="modal-close-btn" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body-custom">
            <div class="bank-account mb-3 p-3 text-center"
                style="background-color: #fef8e5; border: 1px solid #fde08a;">
                <h5 class="modal-title-custom" style="margin-bottom: 5px; font-size: 1.1rem; font-weight: 500;">Total
                    Pembayaran:</h5>
                <h3 id="modal-total-amount"
                    style="font-weight: 700; color: #d9534f; margin-bottom: 0; font-size: 2rem;">
                    Rp...
                </h3>
            </div>
            <p>Pesanan Anda telah kami terima. Silakan selesaikan pembayaran ke salah satu rekening berikut:</p>
            <div class="bank-account mb-3 p-3">
                <strong>MANDIRI</strong><br>
                No. Rekening: <strong>11900-06472-870</strong><br>
                Atas Nama: <strong>Curug Lestarimaju</strong>
            </div>

            <div class="bank-account mb-3 p-3">
                <strong>Bank BCA</strong><br>
                No. Rekening: <strong>00230-25253</strong><br>
                Atas Nama: <strong>CURUG LESTARI MAJU PT</strong>
            </div>

            <hr>

            <p><strong>PENTING:</strong> Setelah melakukan transfer, mohon segera lakukan konfirmasi pembayaran dengan
                menekan tombol di bawah ini.</p>

            <a href="https://wa.me/628988199366?text=Halo%2C%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20pesanan%20..."
                id="modal-wa-link" {{-- <--- TAMBAHKAN INI --}} target="_blank"
                class="btn btn-success btn-block wa-confirm-button">

                Konfirmasi via WhatsApp
            </a>
            <small class="form-text text-muted mt-2 text-center">
                Anda dapat menemukan kembali info pembayaran ini di halaman "Daftar Pesanan" Anda.
            </small>
        </div>
        <div class="modal-footer-custom">
            {{-- Tombol close di footer --}}
            <button type="button" class="btn btn-secondary modal-close-btn">Tutup</button>
        </div>
    </div>
</div>
