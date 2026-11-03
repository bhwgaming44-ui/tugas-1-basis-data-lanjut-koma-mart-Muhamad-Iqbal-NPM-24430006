/**
 * kasir-calc.js - Modul Perhitungan Kasir KOPMA MART (POS)
 * Menghitung subtotal, diskon anggota, kembalian, dan validasi pembayaran.
 */

(function (window) {
    'use strict';

    const KasirCalc = {
        /**
         * Hitung total bruto dan netto dari daftar item
         * @param {Array|Object} items - Koleksi item belanja
         * @param {Boolean} isMember - Status apakah pelanggan adalah anggota aktif
         * @returns {Object} { bruto, netto, diskon }
         */
        calculateTotal: function (items, isMember) {
            const list = Array.isArray(items) ? items : Object.values(items || {});
            let bruto = 0;
            let netto = 0;

            list.forEach(item => {
                const qty = parseInt(item.qty, 10) || 0;
                const hargaUmum = parseFloat(item.hargau || item.harga_umum || 0);
                const hargaAnggota = parseFloat(item.hargaa || item.harga_anggota || hargaUmum);
                const hargaPakai = isMember ? hargaAnggota : hargaUmum;

                bruto += hargaUmum * qty;
                netto += hargaPakai * qty;
            });

            return {
                bruto: bruto,
                netto: netto,
                diskon: Math.max(0, bruto - netto)
            };
        },

        /**
         * Hitung kembalian berdasarkan uang bayar dan total tagihan
         * @param {Number} bayar
         * @param {Number} totalNetto
         * @returns {Number} kembalian (>= 0)
         */
        calculateKembalian: function (bayar, totalNetto) {
            const uang = parseFloat(bayar) || 0;
            const tagihan = parseFloat(totalNetto) || 0;
            return Math.max(0, uang - tagihan);
        },

        /**
         * Format nominal ke format Rupiah standar
         * @param {Number} nominal
         * @returns {String} e.g. "Rp 15.000"
         */
        formatRupiah: function (nominal) {
            const n = Math.round(nominal || 0);
            return 'Rp ' + n.toLocaleString('id-ID');
        }
    };

    window.KasirCalc = KasirCalc;
})(window);
