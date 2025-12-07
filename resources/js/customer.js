document.addEventListener('DOMContentLoaded', () => {

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const app = {
        state: {
            products: [],
            orders: [],
            cart: JSON.parse(localStorage.getItem('flora_cart')) || [],
            paymentMethod: 'Cash On Delivery'
        },

        init() {
            // Load Data
            const prodEl = document.getElementById('db-products-payload');
            const ordEl = document.getElementById('db-orders-payload');

            if (prodEl) try {
                this.state.products = JSON.parse(prodEl.dataset.products);
            } catch (e) {}
            if (ordEl) try {
                this.state.orders = JSON.parse(ordEl.dataset.orders);
            } catch (e) {}

            this.nav.init();
            this.updateBadges();
            this.products.render();
            this.attachListeners();
        },

        save() {
            localStorage.setItem('flora_cart', JSON.stringify(this.state.cart));
            this.updateBadges();
            this.cart.render();
            this.cart.renderCheckout();
        },

        updateBadges() {
            const count = this.state.cart.reduce((a, b) => a + b.qty, 0);
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.innerText = count;
                badge.style.opacity = count > 0 ? 1 : 0;
            }
        },

        toast(msg) {
            const t = document.getElementById('order-toast');
            if (t) {
                t.querySelector('p').innerText = msg;
                t.classList.remove('translate-x-[120%]');
                setTimeout(() => t.classList.add('translate-x-[120%]'), 2000);
            }
        },

        nav: {
            init() {
                this.showView('view-dashboard');
            },
            hideAll() {
                document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
                const bg = document.getElementById('bg-image-container');
                if (bg) bg.style.opacity = '0.5';
            },
            showView(viewId) {
                this.hideAll();
                const view = document.getElementById(viewId);
                if (view) {
                    view.classList.remove('hidden');
                    if (viewId === 'view-dashboard') {
                        const bg = document.getElementById('bg-image-container');
                        if (bg) bg.style.opacity = '1';
                    }
                }
                window.scrollTo(0, 0);
            },
            showPurchases(tab = 'to-ship') {
                this.showView('view-purchases');
                document.querySelectorAll('.purchase-tab').forEach(t => {
                    t.classList.remove('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                    t.classList.add('border-transparent', 'text-gray-500');
                    if (t.dataset.tab === tab) {
                        t.classList.add('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                        t.classList.remove('border-transparent', 'text-gray-500');
                    }
                });
                const container = document.getElementById('purchases-list');
                container.innerHTML = '';
                const filtered = app.state.orders.filter(o => o.status === tab);
                if (filtered.length === 0) {
                    container.innerHTML = `<div class="text-center py-10 text-gray-400">No orders found.</div>`;
                    return;
                }
                filtered.forEach(order => {
                    const div = document.createElement('div');
                    div.className = 'bg-white p-6 rounded-lg shadow mb-4';
                    div.innerHTML = `
                        <div class="flex justify-between border-b pb-2 mb-2">
                            <span class="font-bold text-[#4A4A3A]">#${order.id}</span>
                            <span class="text-sm text-gray-500">${order.date}</span>
                        </div>
                        <div class="mb-2">
                            ${order.items.map(i => `<div class="flex justify-between text-sm text-gray-600"><span>${i.qty}x ${i.name}</span><span>₱${i.price}</span></div>`).join('')}
                        </div>
                        <div class="flex justify-between font-bold text-[#4A4A3A] border-t pt-2"><span>Total</span><span>₱${parseFloat(order.total).toLocaleString()}</span></div>
                        <div class="text-right text-xs uppercase font-bold text-[#86A873] mt-1">${order.real_status}</div>
                    `;
                    container.appendChild(div);
                });
            }
        },

        products: {
            render(filter = 'all', title = 'All Collection') {
                const titleEl = document.getElementById('product-category-title');
                if (titleEl) titleEl.innerText = title;

                const grid = document.getElementById('products-grid');
                if (!grid) return;
                grid.innerHTML = '';

                const items = filter === 'all' ?
                    app.state.products :
                    app.state.products.filter(p => p.category === filter);

                if (items.length === 0) {
                    grid.innerHTML = '<div class="col-span-4 text-center py-10 text-gray-400">No products found.</div>';
                    return;
                }

                items.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all';
                    div.innerHTML = `
                        <div class="h-64 overflow-hidden cursor-pointer open-product-modal" data-id="${p.id}">
                            <img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                        </div>
                        <div class="p-4">
                            <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                            <p class="text-[#86A873] font-bold text-lg">₱${parseFloat(p.price).toLocaleString()}</p>
                            <button class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded mt-2 hover:bg-[#86A873] open-product-modal" data-id="${p.id}">View</button>
                        </div>
                    `;
                    grid.appendChild(div);
                });
            }
        },

        cart: {
            add(p, qty) {
                const exist = app.state.cart.find(i => i.id == p.id);
                if (exist) exist.qty += qty;
                else app.state.cart.push({ ...p,
                    qty
                });
                app.save();
                app.toast('Added to cart!');
            },
            addById(id) {
                const p = app.state.products.find(x => x.id == id);
                if (p) this.add(p, 1);
            },
            remove(id) {
                app.state.cart = app.state.cart.filter(i => i.id != id);
                app.save();
            },
            updateQty(id, delta) {
                const item = app.state.cart.find(i => i.id == id);
                if (item) {
                    item.qty += delta;
                    if (item.qty <= 0) this.remove(id);
                    else app.save();
                }
            },
            render() {
                const tbody = document.getElementById('cart-items-container');
                if (!tbody) return;
                tbody.innerHTML = '';
                let total = 0;
                if (app.state.cart.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-400">Cart empty</td></tr>';
                } else {
                    app.state.cart.forEach(item => {
                        total += item.price * item.qty;
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 flex gap-3 items-center"><img src="${item.image}" class="w-10 h-10 rounded"><span>${item.name}</span></td>
                            <td class="px-6 py-4">₱${item.price}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <button class="text-gray-500 font-bold quantity-btn-minus-cart" data-id="${item.id}">-</button>
                                    <span>${item.qty}</span>
                                    <button class="text-gray-500 font-bold quantity-btn-plus-cart" data-id="${item.id}">+</button>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-[#4A4A3A]">₱${(item.price * item.qty).toLocaleString()}</td>
                            <td class="px-6 py-4"><button class="text-red-500 font-bold cart-remove-btn" data-id="${item.id}">X</button></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
                const totalEl = document.getElementById('cart-total');
                if (totalEl) totalEl.innerText = '₱' + total.toLocaleString();
            },
            renderCheckout() {
                const container = document.getElementById('checkout-cart-items');
                if (!container) return;
                container.innerHTML = '';
                let subtotal = 0;
                app.state.cart.forEach(item => {
                    subtotal += item.price * item.qty;
                    const div = document.createElement('div');
                    div.className = 'flex gap-3 items-center bg-gray-50 p-2 rounded border border-gray-100';
                    div.innerHTML = `<img src="${item.image}" class="w-12 h-12 object-cover rounded"><div class="flex-grow"><h4 class="text-xs font-bold text-[#4A4A3A]">${item.name}</h4><p class="text-xs text-gray-500">₱${parseFloat(item.price).toLocaleString()}</p></div><div class="text-xs font-bold">Qty: ${item.qty}</div>`;
                    container.appendChild(div);
                });
                const fee = 150; // Delivery fee
                const feeEl = document.getElementById('checkout-fee');
                const subEl = document.getElementById('checkout-subtotal');
                const totalEl = document.getElementById('checkout-total');
                const payEl = document.getElementById('payment-total-price');

                if (feeEl) feeEl.innerText = '₱' + fee.toLocaleString();
                if (subEl) subEl.innerText = '₱' + subtotal.toLocaleString();
                if (totalEl) totalEl.innerText = '₱' + (subtotal + fee).toLocaleString();
                if (payEl) payEl.innerText = '₱' + (subtotal + fee).toLocaleString();
            },
            confirmOrder() {
                if (app.state.cart.length === 0) return alert('Cart is empty');

                const btn = document.querySelector('.btn-place-order');
                if (btn) {
                    btn.innerText = 'Processing...';
                    btn.disabled = true;
                }

                const items = app.state.cart.map(i => ({
                    id: i.id,
                    qty: i.qty
                }));
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!csrfToken) {
                    alert("Error: CSRF Token missing. Please refresh the page.");
                    if (btn) {
                        btn.innerText = 'Place Order';
                        btn.disabled = false;
                    }
                    return;
                }

                fetch('/customer/order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            items: items,
                            payment_method: app.state.paymentMethod
                        })
                    })
                    .then(async response => {
                        // 1. Parse JSON response
                        const data = await response.json().catch(() => null);

                        // 2. Check for success
                        if (response.ok && data && data.message) {
                            app.state.cart = [];
                            app.save();
                            document.getElementById('payment-modal').style.display = 'none';
                            document.getElementById('thank-you-modal').style.display = 'flex';
                        } else {
                            // 3. Handle Validation/Server Errors
                            let errorMsg = "Unknown Error";
                            if (data && data.message) errorMsg = data.message;
                            else if (response.status === 419) errorMsg = "Session Expired. Please refresh.";
                            else if (response.status === 500) errorMsg = "Server Error. Check logs.";

                            alert('Order Failed: ' + errorMsg);
                        }
                    })
                    .catch(e => {
                        console.error("Fetch Error:", e);
                        alert('Network Error: Check console for details.');
                    })
                    .finally(() => {
                        if (btn) {
                            btn.innerText = 'PLACE ORDER';
                            btn.disabled = false;
                        }
                    });
            }
        },

        modal: {
            open(id) {
                const p = app.state.products.find(x => x.id == id);
                if (!p) return;
                app.state.currentProduct = p;
                document.getElementById('modal-product-image').src = p.image;
                document.getElementById('modal-product-title').innerText = p.name;
                document.getElementById('modal-product-description').innerText = p.description;
                document.getElementById('modal-product-price').innerText = '₱' + parseFloat(p.price).toLocaleString();
                document.getElementById('modal-quantity-value').innerText = '1';
                document.getElementById('modal-product-id').value = p.id;
                document.getElementById('product-modal').style.display = 'flex';
            },
            close() {
                document.querySelectorAll('.app-container.fixed').forEach(m => m.style.display = 'none');
            },
            adjustQty(delta) {
                const el = document.getElementById('modal-quantity-value');
                let v = parseInt(el.innerText) + delta;
                if (v < 1) v = 1;
                el.innerText = v;
            }
        },

        attachListeners() {
            document.body.addEventListener('click', e => {
                const t = e.target;

                // 1. NAVIGATION
                if (t.closest('.nav-link')) {
                    e.preventDefault();
                    app.nav.showView(t.closest('.nav-link').dataset.target);
                    if (t.closest('.nav-link').dataset.tab) app.nav.showPurchases(t.closest('.nav-link').dataset.tab);
                }

                // 2. FILTERS
                if (t.closest('.nav-link-filter')) {
                    e.preventDefault();
                    const btn = t.closest('.nav-link-filter');
                    app.products.render(btn.dataset.filter, btn.dataset.title);
                    app.nav.showView('view-products');
                }

                // 3. MODAL OPEN/CLOSE
                if (t.closest('.open-product-modal')) app.modal.open(t.closest('.open-product-modal').dataset.id);
                if (t.closest('.modal-close-btn')) app.modal.close();

                // 4. MODAL QUANTITY
                if (t.classList.contains('quantity-btn-plus')) app.modal.adjustQty(1);
                if (t.classList.contains('quantity-btn-minus')) app.modal.adjustQty(-1);

                // 5. ADD TO CART (FROM MODAL)
                if (t.classList.contains('btn-add-cart')) {
                    const qty = parseInt(document.getElementById('modal-quantity-value').innerText);
                    app.cart.add(app.state.currentProduct, qty);
                    app.modal.close();
                }

                // 6. CHECKOUT (FROM MODAL)
                if (t.classList.contains('btn-checkout-modal')) {
                    const qty = parseInt(document.getElementById('modal-quantity-value').innerText);
                    app.cart.add(app.state.currentProduct, qty);
                    app.modal.close();
                    app.nav.showView('view-checkout');
                    app.cart.renderCheckout();
                }

                // 7. CART MANAGEMENT
                if (t.closest('.cart-remove-btn')) app.cart.remove(t.closest('.cart-remove-btn').dataset.id);
                if (t.classList.contains('quantity-btn-plus-cart')) app.cart.updateQty(t.dataset.id, 1);
                if (t.classList.contains('quantity-btn-minus-cart')) app.cart.updateQty(t.dataset.id, -1);

                // 8. PROCEED TO CHECKOUT (FROM CART PAGE)
                if (t.classList.contains('btn-checkout-page')) {
                    if (app.state.cart.length === 0) return alert('Cart empty');
                    app.nav.showView('view-checkout');
                    app.cart.renderCheckout();
                }

                // 9. PAYMENT METHODS
                if (t.classList.contains('payment-btn')) {
                    app.state.paymentMethod = t.dataset.method;
                    document.querySelectorAll('.payment-btn').forEach(b => {
                        b.classList.remove('selected', 'bg-[#86A873]', 'text-white', 'border-[#86A873]');
                        b.classList.add('border-gray-200');
                    });
                    t.classList.add('selected', 'bg-[#86A873]', 'text-white', 'border-[#86A873]');
                    t.classList.remove('border-gray-200');
                }

                // 10. PLACE ORDER
                if (t.classList.contains('btn-place-order')) {
                    app.cart.confirmOrder();
                }

                // 11. BACK TO MAIN
                if (t.classList.contains('btn-back-main')) {
                    app.modal.close();
                    window.location.reload();
                }
            });
        }
    };

    window.app = app;
    app.init();
});