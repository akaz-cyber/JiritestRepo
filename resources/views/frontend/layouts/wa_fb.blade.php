<a id="wa-fab" class="wa-fab" href="#" target="_blank" rel="noopener" aria-label="Chat via WhatsApp">
    <img src="{{ asset('frontend/img/whatsapp.png') }}" alt="WhatsApp"
        style="width: 100%; height: 100%; object-fit: contain;">
</a>

<style>
    .wa-fab {
        position: fixed;
        right: clamp(22px, 3vw, 36px);
        bottom: clamp(50px, 4vh, 56px);
        width: 56px;
        height: 56px;
        display: grid;
        place-items: center;
        background: #02BB39;
        color: #fff;
        border-radius: 999px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
        z-index: 1050;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .2s ease, opacity .2s;
    }

    .wa-fab:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 28px rgba(0, 0, 0, .22);
    }

    .wa-fab:active {
        transform: translateY(0);
    }

    @media (max-width:360px) {
        .wa-fab {
            width: 52px;
            height: 52px;
        }

        /* Opsional: Sesuaikan ukuran gambar di layar kecil */
        .wa-fab img {
            width: 100% !important;
            height: 100% !important;
        }
    }

    @media (max-width: 768px) {
        .wa-fab {
            /* Ubah angka 90px ini. Semakin besar angka, semakin naik ke atas */
            bottom: 65px !important;
        }
    }
</style>

<script>
    (function() {
        // Ganti ke nomor WA kamu (format internasional tanpa + dan tanpa spasi)
        const PHONE = '628988199366';
        const baseMsg = `Halo Admin, saya butuh bantuan terkait `;
        const text = encodeURIComponent(baseMsg);

        const url = `https://wa.me/${PHONE}?text=${text}`;
        document.getElementById('wa-fab').setAttribute('href', url);
    })();
</script>
