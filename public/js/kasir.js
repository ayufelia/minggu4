// ======================================================
// KASIR - JAVASCRIPT
// PEMBAYARAN TUNAI + MIDTRANS
// ======================================================

let cart = [];
let selectedPaymentMethod = "Tunai";
let paymentInProgress = false;


// ======================================================
// FORMAT RUPIAH
// ======================================================

function formatRupiah(number) {
    number = Number(number) || 0;

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(number);
}


// ======================================================
// CSRF TOKEN
// ======================================================

function getCsrfToken() {
    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
}


// ======================================================
// SEARCH PRODUCT
// ======================================================

async function doSearchProduct() {
    const input = document.getElementById("searchProduct");
    const resultBox = document.getElementById("productResults");

    if (!input || !resultBox) {
        console.error("Input pencarian tidak ditemukan.");
        return;
    }

    const keyword = input.value.trim();

    if (keyword === "") {
        resultBox.innerHTML = "";
        resultBox.style.display = "none";
        return;
    }

    try {
        const url = `/kasir/search-product?keyword=${encodeURIComponent(keyword)}`;

        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const products = await response.json();

        resultBox.innerHTML = "";
        resultBox.style.display = "block";

        if (!Array.isArray(products) || products.length === 0) {
            resultBox.innerHTML = `
                <div class="search-empty">
                    Produk tidak ditemukan.
                </div>
            `;
            return;
        }

        products.forEach(function(product) {
            const item = document.createElement("div");

            item.className = "search-result-item";
            item.style.cursor = "pointer";

            item.innerHTML = `
                <div class="search-result-info">
                    <strong>${escapeHtml(product.name)}</strong>
                    <small>Kode: ${escapeHtml(product.sku)}</small>
                </div>

                <div class="search-result-price">
                    ${formatRupiah(product.price)}
                    <small>Stok: ${product.stock}</small>
                </div>
            `;

            item.addEventListener("click", function() {
                addToCart(product);
            });

            resultBox.appendChild(item);
        });

    } catch (error) {
        console.error("ERROR PENCARIAN:", error);

        resultBox.style.display = "block";
        resultBox.innerHTML = `
            <div class="search-empty">
                Gagal mengambil data produk.
                <br>
                <small>${escapeHtml(error.message)}</small>
            </div>
        `;
    }
}


// Fungsi untuk tombol Cari pada Blade
function searchProduct() {
    doSearchProduct();
}


// ======================================================
// ESCAPE HTML
// ======================================================

function escapeHtml(value) {
    if (value === null || value === undefined) {
        return "";
    }

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}


// ======================================================
// TAMBAH KE KERANJANG
// ======================================================

function addToCart(product) {
    const stock = Number(product.stock) || 0;

    if (stock <= 0) {
        alert("Stok produk habis.");
        return;
    }

    const existing = cart.find(function(item) {
        return Number(item.id) === Number(product.id);
    });

    if (existing) {
        if (existing.qty >= stock) {
            alert("Jumlah melebihi stok tersedia.");
            return;
        }

        existing.qty += 1;

    } else {
        cart.push({
            id: Number(product.id),
            name: product.name,
            code: product.sku,
            price: Number(product.price),
            stock: stock,
            qty: 1
        });
    }

    const searchInput = document.getElementById("searchProduct");
    const productResults = document.getElementById("productResults");

    if (searchInput) {
        searchInput.value = "";
    }

    if (productResults) {
        productResults.innerHTML = "";
        productResults.style.display = "none";
    }

    renderCart();
    calculateTotal();
    calculateChange();
}


// ======================================================
// RENDER CART
// ======================================================

function renderCart() {
    const cartBody = document.getElementById("cartBody");

    if (!cartBody) {
        return;
    }

    cartBody.innerHTML = "";

    if (cart.length === 0) {
        cartBody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-cart">
                    Keranjang masih kosong.
                    <br>
                    Silakan cari atau scan barang.
                </td>
            </tr>
        `;

        updateItemCount();
        return;
    }

    cart.forEach(function(item, index) {
        const subtotal = item.price * item.qty;
        const row = document.createElement("tr");

        row.innerHTML = `
            <td>${index + 1}</td>

            <td>
                <strong>${escapeHtml(item.name)}</strong>
                <br>
                <small>${escapeHtml(item.code)}</small>
            </td>

            <td>${formatRupiah(item.price)}</td>

            <td>
                <div class="qty-control">
                    <button
                        type="button"
                        class="qty-button"
                        onclick="decreaseQty(${item.id})">
                        -
                    </button>

                    <span>${item.qty}</span>

                    <button
                        type="button"
                        class="qty-button"
                        onclick="increaseQty(${item.id})">
                        +
                    </button>
                </div>
            </td>

            <td>
                <strong>${formatRupiah(subtotal)}</strong>
            </td>

            <td>
                <button
                    type="button"
                    class="remove-button"
                    onclick="removeFromCart(${item.id})">
                    Hapus
                </button>
            </td>
        `;

        cartBody.appendChild(row);
    });

    updateItemCount();
}


// ======================================================
// JUMLAH ITEM
// ======================================================

function updateItemCount() {
    const totalQty = cart.reduce(function(total, item) {
        return total + item.qty;
    }, 0);

    const itemCount = document.getElementById("itemCount");

    if (itemCount) {
        itemCount.textContent = `${totalQty} Item`;
    }

    const totalQtyElement = document.getElementById("totalQty");

    if (totalQtyElement) {
        totalQtyElement.textContent = totalQty;
    }
}


// ======================================================
// TAMBAH QTY
// ======================================================

function increaseQty(productId) {
    if (paymentInProgress) return;

    const item = cart.find(function(product) {
        return Number(product.id) === Number(productId);
    });

    if (!item) return;

    if (item.qty >= item.stock) {
        alert("Jumlah sudah mencapai stok tersedia.");
        return;
    }

    item.qty += 1;

    renderCart();
    calculateTotal();
    calculateChange();
}


// ======================================================
// KURANGI QTY
// ======================================================

function decreaseQty(productId) {
    if (paymentInProgress) return;

    const item = cart.find(function(product) {
        return Number(product.id) === Number(productId);
    });

    if (!item) return;

    if (item.qty > 1) {
        item.qty -= 1;
    } else {
        removeFromCart(productId);
        return;
    }

    renderCart();
    calculateTotal();
    calculateChange();
}


// ======================================================
// HAPUS PRODUK
// ======================================================

function removeFromCart(productId) {
    if (paymentInProgress) return;

    cart = cart.filter(function(item) {
        return Number(item.id) !== Number(productId);
    });

    renderCart();
    calculateTotal();
    calculateChange();
}


// ======================================================
// SUBTOTAL
// ======================================================

function getSubtotal() {
    return cart.reduce(function(total, item) {
        return total + (item.price * item.qty);
    }, 0);
}


// ======================================================
// DISCOUNT
// ======================================================

function getDiscount() {
    const discountPercent = Number(
        document.getElementById("discountPercent")?.value
    ) || 0;

    const discountAmount = Number(
        document.getElementById("discountAmount")?.value
    ) || 0;

    const subtotal = getSubtotal();

    const percentDiscount = subtotal * (discountPercent / 100);

    return percentDiscount + discountAmount;
}


// ======================================================
// TOTAL
// ======================================================

function calculateTotal() {
    const subtotal = getSubtotal();
    const discount = getDiscount();

    const tax = Number(
        document.getElementById("tax")?.value
    ) || 0;

    const otherFee = Number(
        document.getElementById("otherFee")?.value
    ) || 0;

    const grandTotal = Math.max(
        0,
        subtotal - discount + tax + otherFee
    );

    const subtotalElement = document.getElementById("subtotal");
    const grandTotalElement = document.getElementById("grandTotal");

    if (subtotalElement) {
        subtotalElement.textContent = formatRupiah(subtotal);
    }

    if (grandTotalElement) {
        grandTotalElement.textContent = formatRupiah(grandTotal);
    }

    updateItemCount();

    return grandTotal;
}


// ======================================================
// GRAND TOTAL
// ======================================================

function getGrandTotal() {
    return calculateTotal();
}


// ======================================================
// HITUNG KEMBALIAN
// ======================================================

function calculateChange() {
    const payment = Number(
        document.getElementById("payment")?.value
    ) || 0;

    const total = calculateTotal();
    const change = payment - total;

    const changeElement = document.getElementById("change");
    const changeBox = document.getElementById("changeBox");

    if (changeElement) {
        changeElement.textContent = formatRupiah(
            change >= 0 ? change : 0
        );
    }

    if (changeBox) {
        if (change < 0) {
            changeBox.classList.add("short-payment");
        } else {
            changeBox.classList.remove("short-payment");
        }
    }
}


// ======================================================
// PILIH METODE PEMBAYARAN
// ======================================================

function selectPayment(button) {
    if (paymentInProgress) return;

    document.querySelectorAll(".payment-method button")
        .forEach(function(btn) {
            btn.classList.remove("active");
        });

    button.classList.add("active");

    selectedPaymentMethod =
        button.dataset.method ||
        button.textContent.trim();

    const paymentInput = document.getElementById("payment");

    // Input uang hanya diperlukan untuk pembayaran tunai
    if (paymentInput) {
        if (selectedPaymentMethod === "Tunai") {
            paymentInput.disabled = false;
            paymentInput.placeholder = "Masukkan uang dibayar";
        } else {
            paymentInput.disabled = true;
            paymentInput.value = "";
        }
    }

    calculateChange();
}


// ======================================================
// DATA TRANSAKSI
// ======================================================

function buildTransactionData() {
    return {
        items: cart.map(function(item) {
            return {
                id: item.id,
                qty: item.qty
            };
        }),

        subtotal: getSubtotal(),
        discount: getDiscount(),

        tax: Number(
            document.getElementById("tax")?.value
        ) || 0,

        other_fee: Number(
            document.getElementById("otherFee")?.value
        ) || 0,

        grand_total: getGrandTotal(),

        payment_method: selectedPaymentMethod
    };
}


// ======================================================
// PEMBAYARAN TUNAI
// MENGGUNAKAN CONTROLLER LAMA
// ======================================================

async function processCashPayment() {
    const payment = Number(
        document.getElementById("payment")?.value
    ) || 0;

    const total = getGrandTotal();

    if (payment < total) {
        alert("Pembayaran belum mencukupi.");
        return;
    }

    const data = buildTransactionData();

    data.paid_amount = payment;
    data.change_amount = payment - total;

    const response = await fetch("/kasir/transaksi", {
        method: "POST",

        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": getCsrfToken()
        },

        body: JSON.stringify(data)
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(
            result.message || "Transaksi tunai gagal."
        );
    }

    if (result.redirect) {
        window.location.href = result.redirect;
        return;
    }

    alert("Transaksi berhasil.");

    window.location.reload();
}


// ======================================================
// PEMBAYARAN MIDTRANS
// ======================================================

async function processMidtransPayment() {
    // Pastikan Snap sudah dimuat
    if (typeof window.snap === "undefined") {
        throw new Error(
            "Midtrans Snap belum dimuat. Periksa Client Key dan koneksi internet."
        );
    }

    const data = buildTransactionData();

    const response = await fetch(
        "/kasir/midtrans/create-payment",
        {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": getCsrfToken()
            },

            body: JSON.stringify(data)
        }
    );

    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(
            result.message || "Gagal membuat pembayaran Midtrans."
        );
    }

    if (!result.snap_token) {
        throw new Error("Snap Token tidak ditemukan.");
    }

    // Buka halaman pembayaran Midtrans
    window.snap.pay(result.snap_token, {

        onSuccess: function(paymentResult) {
            console.log("Pembayaran berhasil:", paymentResult);

            alert(
                "Pembayaran berhasil dikirim ke Midtrans. " +
                "Status transaksi akan diperbarui melalui notifikasi server."
            );

            cart = [];

            renderCart();
            calculateTotal();
            calculateChange();

            const paymentInput = document.getElementById("payment");

            if (paymentInput) {
                paymentInput.value = "";
                paymentInput.disabled = false;
            }

            paymentInProgress = false;
        },

        onPending: function(paymentResult) {
            console.log("Pembayaran menunggu:", paymentResult);

            alert(
                "Pembayaran masih menunggu penyelesaian. " +
                "Silakan selesaikan pembayaran sesuai instruksi Midtrans. " +
                "Jangan membuat transaksi baru untuk pesanan yang sama."
            );

            // Jangan mengosongkan keranjang.
            // Transaksi sudah tercatat sebagai pending.
            paymentInProgress = false;
        },

        onError: function(paymentResult) {
            console.error("Pembayaran gagal:", paymentResult);

            alert(
                "Pembayaran gagal. Silakan coba kembali."
            );

            paymentInProgress = false;
        },

        onClose: function() {
            alert(
                "Halaman pembayaran ditutup. " +
                "Periksa status transaksi sebelum mencoba membayar lagi."
            );

            paymentInProgress = false;
        }
    });
}


// ======================================================
// PROSES PEMBAYARAN UTAMA
// ======================================================

async function processPayment() {
    if (paymentInProgress) {
        alert("Pembayaran sedang diproses.");
        return;
    }

    if (cart.length === 0) {
        alert("Keranjang masih kosong.");
        return;
    }

    const total = getGrandTotal();

    if (total <= 0) {
        alert("Total pembayaran tidak valid.");
        return;
    }

    paymentInProgress = true;

    const payButton = document.querySelector(".btn-pay");

    if (payButton) {
        payButton.disabled = true;
        payButton.textContent = "MEMPROSES...";
    }

    try {
        if (selectedPaymentMethod === "Tunai") {
            await processCashPayment();
        } else {
            await processMidtransPayment();
        }

    } catch (error) {
        console.error("ERROR PEMBAYARAN:", error);

        alert(
            error.message || "Terjadi kesalahan saat pembayaran."
        );

        paymentInProgress = false;

    } finally {
        if (payButton) {
            payButton.disabled = false;
            payButton.textContent = "BAYAR & CETAK";
        }
    }
}


// ======================================================
// HOLD TRANSACTION
// ======================================================

function holdTransaction() {
    if (paymentInProgress) return;

    if (cart.length === 0) {
        alert("Tidak ada transaksi untuk ditahan.");
        return;
    }

    alert(
        "Fitur tahan transaksi akan dibuat pada tahap berikutnya."
    );
}


// ======================================================
// CANCEL TRANSACTION
// ======================================================

function cancelTransaction() {
    if (paymentInProgress) return;

    const confirmation = confirm(
        "Apakah kamu yakin ingin membatalkan transaksi?"
    );

    if (!confirmation) return;

    cart = [];

    renderCart();
    calculateTotal();
    calculateChange();

    const payment = document.getElementById("payment");

    if (payment) {
        payment.value = "";
        payment.disabled = false;
    }

    const searchInput = document.getElementById("searchProduct");
    const productResults = document.getElementById("productResults");

    if (searchInput) {
        searchInput.value = "";
    }

    if (productResults) {
        productResults.innerHTML = "";
        productResults.style.display = "none";
    }

    // Kembalikan metode ke Tunai
    selectedPaymentMethod = "Tunai";

    document.querySelectorAll(".payment-method button")
        .forEach(function(button) {
            button.classList.remove("active");

            if (button.textContent.trim() === "Tunai") {
                button.classList.add("active");
            }
        });
}


// ======================================================
// TANGGAL
// ======================================================

function setCurrentDate() {
    const currentDate = document.getElementById("currentDate");

    if (!currentDate) return;

    currentDate.textContent = new Date().toLocaleString("id-ID");
}


// ======================================================
// SEARCH ENTER
// ======================================================

function setupSearch() {
    const searchInput = document.getElementById("searchProduct");

    if (!searchInput) return;

    searchInput.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            doSearchProduct();
        }
    });
}


// ======================================================
// KEYBOARD SHORTCUT
// ======================================================

function setupKeyboardShortcut() {
    document.addEventListener("keydown", function(event) {

        if (event.key === "F2") {
            event.preventDefault();

            document.getElementById("searchProduct")?.focus();
        }

        if (event.key === "F4") {
            event.preventDefault();

            document.getElementById("payment")?.focus();
        }

        if (event.key === "Escape") {
            cancelTransaction();
        }
    });
}


// ======================================================
// EVENT INPUT PEMBAYARAN
// ======================================================

function setupPaymentInput() {
    const payment = document.getElementById("payment");

    if (!payment) return;

    payment.addEventListener("input", function() {
        calculateChange();
    });
}


// ======================================================
// EVENT DISKON / PAJAK / BIAYA
// ======================================================

function setupCalculationInputs() {
    const fields = [
        "discountPercent",
        "discountAmount",
        "tax",
        "otherFee"
    ];

    fields.forEach(function(id) {
        const element = document.getElementById(id);

        if (!element) return;

        element.addEventListener("input", function() {
            calculateTotal();
            calculateChange();
        });
    });
}


// ======================================================
// INITIAL
// ======================================================

document.addEventListener("DOMContentLoaded", function() {
    console.log("KASIR JS BERHASIL DIMUAT");

    setCurrentDate();

    setupSearch();
    setupKeyboardShortcut();
    setupPaymentInput();
    setupCalculationInputs();

    renderCart();
    calculateTotal();
    calculateChange();
});