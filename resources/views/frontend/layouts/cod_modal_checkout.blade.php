<div id="customCodModal" class="ccm-overlay">
    <div class="ccm-box">
        <div class="ccm-header">
            <h3>Informasi Pengambilan</h3>
            <button type="button" class="ccm-close-icon" onclick="closeCustomCodModal()">&times;</button>
        </div>

        <div class="ccm-body">
            <div class="ccm-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>

            <h2 class="ccm-title">Pesanan Berhasil!</h2>
            <p class="ccm-desc">Silakan hubungi kami untuk konfirmasi jadwal pengambilan barang.</p>

            <div class="ccm-details">
                <div class="ccm-row">
                    <span>No. Pesanan</span>
                    <strong id="ccm-order-number">...</strong>
                </div>
                <div class="ccm-row">
                    <span>Total Bayar</span>
                    <strong id="ccm-total-amount" class="ccm-price">...</strong>
                </div>
            </div>

            <a href="#" id="ccm-wa-link" target="_blank" class="ccm-btn-wa">
                Hubungi via WhatsApp
            </a>
        </div>

        <div class="ccm-footer">
            <button type="button" class="ccm-btn-close" onclick="closeCustomCodModal()">Tutup & Lihat Pesanan</button>
        </div>
    </div>
</div>

<style>
    /* Reset & Base Styles untuk Modal ini saja */
    .ccm-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        /* Gelap transparan */
        backdrop-filter: blur(3px);
        /* Efek blur di belakang */
        z-index: 99999;
        /* Pastikan paling atas */
        display: none;
        /* Hidden by default */
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
        /* Sesuaikan font */
    }

    .ccm-overlay.active {
        display: flex;
        animation: ccmFadeIn 0.3s ease;
    }

    .ccm-box {
        background: #fff;
        width: 90%;
        max-width: 420px;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        position: relative;
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .ccm-overlay.active .ccm-box {
        transform: scale(1);
    }

    /* Header */
    .ccm-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ccm-header h3 {
        margin: 0;
        font-size: 16px;
        color: #444;
        font-weight: 600;
    }

    .ccm-close-icon {
        background: none;
        border: none;
        font-size: 24px;
        line-height: 1;
        cursor: pointer;
        color: #888;
    }

    /* Body */
    .ccm-body {
        padding: 30px 25px;
        text-align: center;
    }

    .ccm-icon-wrapper {
        width: 70px;
        height: 70px;
        background: #e6fcf5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .ccm-icon-wrapper svg {
        width: 35px;
        height: 35px;
        stroke: #20c997;
    }

    .ccm-title {
        margin: 0 0 10px;
        font-size: 22px;
        color: #333;
        font-weight: 700;
    }

    .ccm-desc {
        margin: 0 0 25px;
        color: #666;
        font-size: 14px;
        line-height: 1.5;
    }

    /* Details Box */
    .ccm-details {
        background: #f8f9fa;
        border: 1px dashed #ddd;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 25px;
    }

    .ccm-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 14px;
        color: #555;
    }

    .ccm-row:last-child {
        margin-bottom: 0;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #eee;
    }

    .ccm-price {
        color: #F7941D;
        /* Warna Jirifarm */
        font-size: 16px;
    }

    /* Buttons */
    .ccm-btn-wa {
        display: block;
        width: 100%;
        background: #25D366;
        color: white;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.2s;
        border: none;
        font-size: 15px;
    }

    .ccm-btn-wa:hover {
        background: #1ebe57;
        color: white;
        text-decoration: none;
    }

    .ccm-footer {
        padding: 15px 25px;
        text-align: center;
    }

    .ccm-btn-close {
        background: transparent;
        border: 1px solid #ddd;
        padding: 10px 20px;
        border-radius: 6px;
        color: #666;
        cursor: pointer;
        width: 100%;
        transition: all 0.2s;
    }

    .ccm-btn-close:hover {
        background: #eee;
        color: #333;
    }

    @keyframes ccmFadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>

<script>
    // Global variable untuk menyimpan URL redirect
    var ccmRedirectUrl = "{{ route('home') }}";

    function openCustomCodModal(orderNo, total, waLink, redirect) {
        // Set data
        document.getElementById('ccm-order-number').innerText = orderNo;
        document.getElementById('ccm-total-amount').innerText = total;
        document.getElementById('ccm-wa-link').href = waLink;

        // Simpan redirect url
        if (redirect) ccmRedirectUrl = redirect;

        // Show modal dengan mengubah class
        document.getElementById('customCodModal').classList.add('active');
    }

    function closeCustomCodModal() {
        // Hide modal
        document.getElementById('customCodModal').classList.remove('active');

        // Redirect setelah delay sedikit
        setTimeout(function() {
            window.location.replace(ccmRedirectUrl);
        }, 300);
    }
</script>
