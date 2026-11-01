import './bootstrap';

// Menerima notifikasi transaksi baru dari Laravel Reverb
window.Echo.channel('transactions')
    .listen('.order.placed', (event) => {

        console.log('Transaksi baru diterima:', event);

        const nomorTransaksi =
            event.transaction?.transaction_number ?? 'Transaksi baru';

        alert('Transaksi berhasil dibuat: ' + nomorTransaksi);

    });