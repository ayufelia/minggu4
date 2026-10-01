// ======================================================
// KASIR - JAVASCRIPT
// ======================================================

let cart = [];

let selectedPaymentMethod = "Tunai";


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
// SEARCH PRODUCT
// ======================================================

async function doSearchProduct() {

    const input = document.getElementById("searchProduct");
    const resultBox = document.getElementById("productResults");

    if (!input || !resultBox) {
        console.error("Input pencarian atau hasil pencarian tidak ditemukan.");
        return;
    }

    const keyword = input.value.trim();

    if (keyword === "") {

        resultBox.innerHTML = "";
        resultBox.style.display = "none";

        return;
    }

    try {

        console.log("Mencari produk:", keyword);

        const url =
            `/kasir/search-product?keyword=${encodeURIComponent(keyword)}`;

        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        console.log("Status pencarian:", response.status);

        if (!response.ok) {

            throw new Error(
                `HTTP ${response.status} - ${response.statusText}`
            );
        }

        const products = await response.json();

        console.log("Produk dari database:", products);

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


        // ==================================================
        // TAMPILKAN HASIL PRODUK
        // ==================================================

        products.forEach(function (product) {

            const item = document.createElement("div");

            item.className = "search-result-item";

            item.style.cursor = "pointer";

            item.innerHTML = `

                <div class="search-result-info">

                    <strong>
                        ${escapeHtml(product.name)}
                    </strong>

                    <small>
                        Kode: ${escapeHtml(product.sku)}
                    </small>

                </div>

                <div class="search-result-price">

                    ${formatRupiah(product.price)}

                    <small>
                        Stok: ${product.stock}
                    </small>

                </div>

            `;


            item.addEventListener("click", function () {

                addToCart(product);

            });


            resultBox.appendChild(item);

        });

    } catch (error) {

        console.error(
            "ERROR PENCARIAN PRODUK:",
            error
        );

        resultBox.style.display = "block";

        resultBox.innerHTML = `

            <div class="search-empty">

                Gagal mengambil data produk.

                <br>

                <small>
                    ${escapeHtml(error.message)}
                </small>

            </div>

        `;
    }
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

    const stock =
        Number(product.stock) || 0;


    if (stock <= 0) {

        alert("Stok produk habis.");

        return;
    }


    const existing =
        cart.find(function (item) {

            return Number(item.id) ===
                Number(product.id);

        });


    if (existing) {

        if (existing.qty >= stock) {

            alert(
                "Jumlah melebihi stok tersedia."
            );

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


    const searchInput =
        document.getElementById("searchProduct");

    const productResults =
        document.getElementById("productResults");


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

    const cartBody =
        document.getElementById("cartBody");


    if (!cartBody) {
        return;
    }


    cartBody.innerHTML = "";


    if (cart.length === 0) {

        cartBody.innerHTML = `

            <tr>

                <td
                    colspan="6"
                    class="empty-cart"
                >

                    Keranjang masih kosong.

                    <br>

                    Silakan cari atau scan barang.

                </td>

            </tr>

        `;

        updateItemCount();

        return;
    }


    cart.forEach(function (item, index) {

        const subtotal =
            item.price * item.qty;


        const row =
            document.createElement("tr");


        row.innerHTML = `

            <td>
                ${index + 1}
            </td>


            <td>

                <strong>
                    ${escapeHtml(item.name)}
                </strong>

                <br>

                <small>
                    ${escapeHtml(item.code)}
                </small>

            </td>


            <td>
                ${formatRupiah(item.price)}
            </td>


            <td>

                <div class="qty-control">

                    <button
                        type="button"
                        class="qty-button"
                        onclick="decreaseQty(${item.id})"
                    >
                        -
                    </button>


                    <span>
                        ${item.qty}
                    </span>


                    <button
                        type="button"
                        class="qty-button"
                        onclick="increaseQty(${item.id})"
                    >
                        +
                    </button>

                </div>

            </td>


            <td>

                <strong>
                    ${formatRupiah(subtotal)}
                </strong>

            </td>


            <td>

                <button
                    type="button"
                    class="remove-button"
                    onclick="removeFromCart(${item.id})"
                >
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

    const totalQty =
        cart.reduce(function (total, item) {

            return total + item.qty;

        }, 0);


    const itemCount =
        document.getElementById("itemCount");


    if (itemCount) {

        itemCount.textContent =
            `${totalQty} Item`;

    }


    const totalQtyElement =
        document.getElementById("totalQty");


    if (totalQtyElement) {

        totalQtyElement.textContent =
            totalQty;

    }
}


// ======================================================
// TAMBAH QTY
// ======================================================

function increaseQty(productId) {

    const item =
        cart.find(function (product) {

            return Number(product.id) ===
                Number(productId);

        });


    if (!item) {
        return;
    }


    if (item.qty >= item.stock) {

        alert(
            "Jumlah sudah mencapai stok tersedia."
        );

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

    const item =
        cart.find(function (product) {

            return Number(product.id) ===
                Number(productId);

        });


    if (!item) {
        return;
    }


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

    cart =
        cart.filter(function (item) {

            return Number(item.id) !==
                Number(productId);

        });


    renderCart();

    calculateTotal();

    calculateChange();
}


// ======================================================
// SUBTOTAL
// ======================================================

function getSubtotal() {

    return cart.reduce(function (total, item) {

        return total +
            (item.price * item.qty);

    }, 0);
}


// ======================================================
// DISCOUNT
// ======================================================

function getDiscount() {

    const discountPercentElement =
        document.getElementById("discountPercent");

    const discountAmountElement =
        document.getElementById("discountAmount");


    const discountPercent =
        Number(
            discountPercentElement?.value
        ) || 0;


    const discountAmount =
        Number(
            discountAmountElement?.value
        ) || 0;


    const subtotal =
        getSubtotal();


    const percentDiscount =
        subtotal *
        (discountPercent / 100);


    return percentDiscount +
        discountAmount;
}


// ======================================================
// TOTAL
// ======================================================

function calculateTotal() {

    const subtotal =
        getSubtotal();


    const discount =
        getDiscount();


    const tax =
        Number(
            document.getElementById("tax")?.value
        ) || 0;


    const otherFee =
        Number(
            document.getElementById("otherFee")?.value
        ) || 0;


    const grandTotal =
        Math.max(
            0,
            subtotal -
            discount +
            tax +
            otherFee
        );


    const subtotalElement =
        document.getElementById("subtotal");


    const grandTotalElement =
        document.getElementById("grandTotal");


    if (subtotalElement) {

        subtotalElement.textContent =
            formatRupiah(subtotal);

    }


    if (grandTotalElement) {

        grandTotalElement.textContent =
            formatRupiah(grandTotal);

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

    const paymentElement =
        document.getElementById("payment");


    const payment =
        Number(
            paymentElement?.value
        ) || 0;


    const total =
        calculateTotal();


    const change =
        payment - total;


    const changeElement =
        document.getElementById("change");


    const changeBox =
        document.getElementById("changeBox");


    if (changeElement) {

        if (change >= 0) {

            changeElement.textContent =
                formatRupiah(change);

        } else {

            changeElement.textContent =
                formatRupiah(0);

        }

    }


    if (changeBox) {

        if (change < 0) {

            changeBox.classList.add(
                "short-payment"
            );

        } else {

            changeBox.classList.remove(
                "short-payment"
            );

        }

    }
}


// ======================================================
// PAYMENT METHOD
// ======================================================

function selectPayment(button) {

    document
        .querySelectorAll(
            ".payment-method button"
        )
        .forEach(function (btn) {

            btn.classList.remove(
                "active"
            );

        });


    button.classList.add("active");


    selectedPaymentMethod =
        button.dataset.method ||
        button.textContent.trim();
}


// ======================================================
// PROSES PEMBAYARAN
// ======================================================

async function processPayment() {

    if (cart.length === 0) {

        alert(
            "Keranjang masih kosong."
        );

        return;
    }


    const payment =
        Number(
            document.getElementById(
                "payment"
            )?.value
        ) || 0;


    const total =
        calculateTotal();


    if (payment < total) {

        alert(
            "Pembayaran belum mencukupi."
        );

        return;
    }


    const change =
        payment - total;


    const subtotal =
        getSubtotal();


    const discount =
        getDiscount();


    const tax =
        Number(
            document.getElementById("tax")?.value
        ) || 0;


    const otherFee =
        Number(
            document.getElementById("otherFee")?.value
        ) || 0;


    const data = {

        items: cart.map(function (item) {

            return {

                id: item.id,

                qty: item.qty

            };

        }),

        subtotal: subtotal,

        discount: discount,

        tax: tax,

        other_fee: otherFee,

        grand_total: total,

        paid_amount: payment,

        change_amount: change,

        payment_method:
            selectedPaymentMethod

    };


    try {

        const csrfToken =
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                ?.getAttribute("content");


        const response =
            await fetch(
                "/kasir/transaksi",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Accept":
                            "application/json",

                        "X-CSRF-TOKEN":
                            csrfToken

                    },

                    body:
                        JSON.stringify(data)

                }
            );


        const result =
            await response.json();


        if (!response.ok) {

            throw new Error(
                result.message ||
                "Transaksi gagal diproses."
            );

        }


        if (result.success) {

            window.location.href =
                result.redirect;

        } else {

            alert(
                result.message ||
                "Transaksi gagal."
            );

        }

    } catch (error) {

        console.error(error);

        alert(
            error.message ||
            "Terjadi kesalahan saat memproses transaksi."
        );

    }
}


// ======================================================
// HOLD TRANSACTION
// ======================================================

function holdTransaction() {

    if (cart.length === 0) {

        alert(
            "Tidak ada transaksi untuk ditahan."
        );

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

    const confirmation =
        confirm(
            "Apakah kamu yakin ingin membatalkan transaksi?"
        );


    if (!confirmation) {
        return;
    }


    cart = [];


    renderCart();

    calculateTotal();

    calculateChange();


    const payment =
        document.getElementById("payment");


    if (payment) {

        payment.value = "";

    }


    const searchInput =
        document.getElementById("searchProduct");


    const productResults =
        document.getElementById("productResults");


    if (searchInput) {

        searchInput.value = "";

    }


    if (productResults) {

        productResults.innerHTML = "";

        productResults.style.display = "none";

    }
}


// ======================================================
// TANGGAL
// ======================================================

function setCurrentDate() {

    const currentDate =
        document.getElementById(
            "currentDate"
        );


    if (!currentDate) {
        return;
    }


    currentDate.textContent =
        new Date().toLocaleString(
            "id-ID"
        );
}


// ======================================================
// SEARCH ENTER
// ======================================================

function setupSearch() {

    const searchInput =
        document.getElementById(
            "searchProduct"
        );


    if (!searchInput) {
        return;
    }


    searchInput.addEventListener(
        "keydown",
        function (event) {

            if (event.key === "Enter") {

                event.preventDefault();

                doSearchProduct();

            }

        }
    );
}


// ======================================================
// KEYBOARD SHORTCUT
// ======================================================

function setupKeyboardShortcut() {

    document.addEventListener(
        "keydown",
        function (event) {

            // F2 = pencarian
            if (event.key === "F2") {

                event.preventDefault();

                document
                    .getElementById(
                        "searchProduct"
                    )
                    ?.focus();

            }


            // F4 = pembayaran
            if (event.key === "F4") {

                event.preventDefault();

                document
                    .getElementById(
                        "payment"
                    )
                    ?.focus();

            }


            // ESC = batal
            if (event.key === "Escape") {

                cancelTransaction();

            }

        }
    );
}


// ======================================================
// EVENT INPUT PEMBAYARAN
// ======================================================

function setupPaymentInput() {

    const payment =
        document.getElementById(
            "payment"
        );


    if (!payment) {
        return;
    }


    payment.addEventListener(
        "input",
        function () {

            calculateChange();

        }
    );
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


    fields.forEach(function (id) {

        const element =
            document.getElementById(id);


        if (!element) {
            return;
        }


        element.addEventListener(
            "input",
            function () {

                calculateTotal();

                calculateChange();

            }
        );

    });
}


// ======================================================
// INITIAL
// ======================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "KASIR JS BERHASIL DIMUAT"
        );


        setCurrentDate();

        setupSearch();

        setupKeyboardShortcut();

        setupPaymentInput();

        setupCalculationInputs();

        renderCart();

        calculateTotal();

        calculateChange();

    }
);