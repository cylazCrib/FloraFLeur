document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const app = {
        state: {
            products: [], orders: [],
            cart: JSON.parse(localStorage.getItem('flora_cart')) || [],
            favorites: JSON.parse(localStorage.getItem('flora_favs')) || [],
            paymentMethod: 'Cash On Delivery',
            requests: []
        },

        init() {
            // Load Data
            const prodEl = document.getElementById('db-products-payload');
            const ordEl = document.getElementById('db-orders-payload');
            const reqEl = document.getElementById('db-requests-payload');

            if(prodEl) try{ this.state.products = JSON.parse(prodEl.dataset.products); } catch(e){}
            if(ordEl) try{ this.state.orders = JSON.parse(ordEl.dataset.orders); } catch(e){}
            if(reqEl) try{ this.state.requests = JSON.parse(reqEl.dataset.requests); } catch(e){}

            this.nav.init();
            this.updateBadges();
            this.products.render('all', 'All Collection'); 
            this.attachListeners();
        },

        save() {
            localStorage.setItem('flora_cart', JSON.stringify(this.state.cart));
            localStorage.setItem('flora_favs', JSON.stringify(this.state.favorites));
            this.updateBadges();
            this.cart.render();
            this.cart.renderCheckout();
        },

        updateBadges() {
            const count = this.state.cart.reduce((a,b) => a+b.qty, 0);
            const badge = document.getElementById('cart-badge');
            if(badge) { 
                badge.innerText = count; 
                badge.style.opacity = count > 0 ? 1 : 0; 
            }
        },

        nav: {
            init() { this.showView('view-dashboard'); },
            hideAll() {
                document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.app-container').forEach(el => el.classList.remove('active'));
            },
            showView(target) {
                this.hideAll();
                const container = document.getElementById('app-container-dashboard');
                if(container) container.classList.add('active');
                const page = document.getElementById(target);
                if(page) { page.classList.remove('hidden'); page.classList.add('active'); }
                const bg = document.getElementById('bg-image-container');
                if(bg) bg.style.opacity = (target === 'view-dashboard') ? '1' : '0.5';
                window.scrollTo(0,0);
            },
            showPurchases(tab) {
                this.showView('view-purchases');
                document.querySelectorAll('.purchase-tab').forEach(t => {
                    t.classList.remove('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                    t.classList.add('border-transparent', 'text-gray-500');
                    if(t.dataset.tab === tab) {
                        t.classList.add('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                        t.classList.remove('border-transparent', 'text-gray-500');
                    }
                });
                const container = document.getElementById('purchases-list');
                container.innerHTML = '';
                
                // REQUESTS
                if (tab === 'requests') {
                    if (!app.state.requests || app.state.requests.length === 0) {
                        container.innerHTML = `<div class="text-center py-10 text-gray-400">No requests found.</div>`;
                        return;
                    }
                    app.state.requests.forEach(r => {
                        let color = 'text-gray-500';
                        if(r.status==='Accepted' || r.status==='Being Made') color='text-blue-600';
                        if(r.status==='Completed') color='text-green-600';
                        if(r.status==='Rejected') color='text-red-600';
                        container.innerHTML += `
                            <div class="bg-white p-6 rounded-lg shadow mb-4 text-gray-800">
                                <div class="flex justify-between border-b pb-2 mb-2"><span class="font-bold">Request</span><span class="text-sm">${r.date}</span></div>
                                <div class="mb-2 italic text-sm">"${r.description}"</div>
                                <div class="flex justify-between font-bold border-t pt-2 items-center">
                                    <span>Budget: ${r.budget ? '₱'+parseFloat(r.budget).toLocaleString() : '-'}</span>
                                    <span class="text-xs uppercase font-bold ${color}">${r.status}</span>
                                </div>
                            </div>`;
                    });
                    return;
                }

                // ORDERS
                const filtered = app.state.orders.filter(o => o.status === tab || (tab==='to-ship' && (o.status!=='Delivered' && o.status!=='Completed' && o.status!=='Canceled')));
                if(filtered.length === 0) { container.innerHTML = `<div class="text-center py-10 text-gray-400">No orders found.</div>`; return; }
                filtered.forEach(o => {
                    const itemsHtml = o.items.map(i => `<div class="flex justify-between text-sm text-gray-600"><span>${i.qty}x ${i.name}</span><span>₱${parseFloat(i.price).toLocaleString()}</span></div>`).join('');
                    container.innerHTML += `
                        <div class="bg-white p-6 rounded-lg shadow mb-4 text-gray-800">
                            <div class="flex justify-between border-b pb-2 mb-2"><span class="font-bold">#${o.id}</span><span class="text-sm">${o.date}</span></div>
                            <div class="mb-2 space-y-1">${itemsHtml}</div>
                            <div class="flex justify-between font-bold border-t pt-2"><span>Total</span><span>₱${parseFloat(o.total).toLocaleString()}</span></div>
                            <div class="text-right text-xs uppercase font-bold text-[#86A873] mt-1">${o.status}</div>
                        </div>`;
                });
            }
        },

        products: {
            render(filterType, filterValue) {
                const titles = { 'bouquet':'Bouquets', 'basket':'Baskets', 'box':'Boxes', 'standee':'Standees', 'potted':'Plants', 'valentines':"Valentine's", 'wedding':'Wedding', 'all':'All Collection' };
                document.getElementById('product-category-title').innerText = titles[filterValue] || 'Collection';
                
                const grid = document.getElementById('products-grid');
                grid.innerHTML = '';
                
                const items = app.state.products.filter(p => {
                    if (filterType === 'all') return true;
                    if (filterType === 'occasion') return p.occasion === filterValue;
                    if (filterType === 'category') return p.category === filterValue;
                });

                if (items.length === 0) { grid.innerHTML = '<div class="col-span-4 text-center py-10 text-gray-400">No products found.</div>'; return; }
                
                items.forEach(p => {
                    const isFav = app.state.favorites.some(f => f.id === p.id);
                    const heartClass = isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-400';
                    grid.innerHTML += `
                        <div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all relative">
                            <button class="fav-btn absolute top-3 right-3 z-10 bg-white/80 p-2 rounded-full shadow hover:bg-white transition" data-id="${p.id}">
                                <i class="${heartClass} fa-heart"></i>
                            </button>
                            <div class="h-64 overflow-hidden cursor-pointer open-product-modal" data-id="${p.id}">
                                <img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                            </div>
                            <div class="p-4">
                                <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                                <p class="text-[#86A873] font-bold text-lg">₱${parseFloat(p.price).toLocaleString()}</p>
                                <button class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded mt-2 hover:bg-[#86A873] open-product-modal" data-id="${p.id}">View</button>
                            </div>
                        </div>`;
                });
            }
        },

        cart: {
            add(p, qty) {
                const exist = app.state.cart.find(i => i.id == p.id);
                if(exist) exist.qty += qty; else app.state.cart.push({...p, qty});
                app.save();
                alert('Added to Cart');
            },
            remove(id) { app.state.cart = app.state.cart.filter(i => i.id != id); app.save(); },
            render() {
                const tbody = document.getElementById('cart-items-container');
                tbody.innerHTML = '';
                let total = 0;
                if(app.state.cart.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4">Cart empty</td></tr>'; }
                else {
                    app.state.cart.forEach(item => {
                        total += item.price * item.qty;
                        tbody.innerHTML += `<tr class="border-b"><td class="px-6 py-4 flex gap-3 items-center"><img src="${item.image}" class="w-10 h-10 rounded"><span>${item.name}</span></td><td class="px-6 py-4">₱${item.price}</td><td class="px-6 py-4">${item.qty}</td><td class="px-6 py-4">₱${item.price*item.qty}</td><td class="px-6 py-4"><button class="text-red-500 cart-remove-btn" data-id="${item.id}">X</button></td></tr>`;
                    });
                }
                document.getElementById('cart-total').innerText = '₱'+total.toLocaleString();
            },
            renderCheckout() {
                 const div = document.getElementById('checkout-cart-items');
                 div.innerHTML = '';
                 let total = 0;
                 app.state.cart.forEach(item => {
                     total += item.price * item.qty;
                     div.innerHTML += `<div class="flex justify-between text-sm"><span>${item.qty}x ${item.name}</span><span>₱${(item.price*item.qty).toLocaleString()}</span></div>`;
                 });
                 document.getElementById('checkout-total').innerText = '₱'+total.toLocaleString();
            },
            confirmOrder() {
                if(app.state.cart.length===0) return alert('Cart empty');
                const items = app.state.cart.map(i => ({ id: i.id, qty: i.qty }));
                fetch('/customer/order', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ items, payment_method: app.state.paymentMethod }) })
                .then(res => res.json()).then(data => {
                    if(data.message) { app.state.cart = []; app.save(); document.getElementById('thank-you-modal').style.display = 'flex'; }
                });
            }
        },

        modal: {
            open(id) {
                const p = app.state.products.find(x => x.id == id);
                if(!p) return;
                app.state.currentProduct = p;
                document.getElementById('modal-product-image').src = p.image;
                document.getElementById('modal-product-title').innerText = p.name;
                document.getElementById('modal-product-description').innerText = p.description;
                document.getElementById('modal-product-price').innerText = '₱' + parseFloat(p.price).toLocaleString();
                document.getElementById('modal-quantity-value').innerText = '1';
                document.getElementById('product-modal').style.display = 'flex';
            },
            close() { document.querySelectorAll('.app-container.fixed').forEach(m => m.style.display = 'none'); },
            adjustQty(d) {
                const el = document.getElementById('modal-quantity-value');
                let v = parseInt(el.innerText) + d;
                if(v < 1) v = 1;
                el.innerText = v;
            }
        },

        attachListeners() {
            document.body.addEventListener('click', e => {
                const t = e.target;
                
                // NAV
                if (t.closest('.nav-link')) {
                    e.preventDefault();
                    app.nav.showView(t.closest('.nav-link').dataset.target);
                    if(t.closest('.nav-link').dataset.target === 'view-cart') app.cart.render();
                    if(t.closest('.nav-link').dataset.target === 'view-purchases') app.nav.showPurchases('to-ship');
                }

                // FILTER
                if (t.closest('.filter-btn')) {
                    e.preventDefault();
                    const btn = t.closest('.filter-btn');
                    app.products.render(btn.dataset.type, btn.dataset.value);
                    app.nav.showView('view-products');
                }

                // MODAL & BUTTONS
                if (t.closest('.open-product-modal')) app.modal.open(t.closest('.open-product-modal').dataset.id);
                if (t.closest('.modal-close-btn')) app.modal.close();
                if (t.classList.contains('quantity-btn-plus')) app.modal.adjustQty(1);
                if (t.classList.contains('quantity-btn-minus')) app.modal.adjustQty(-1);
                
                if (t.classList.contains('btn-add-cart')) {
                    const qty = parseInt(document.getElementById('modal-quantity-value').innerText);
                    app.cart.add(app.state.currentProduct, qty);
                    app.modal.close();
                }
                if (t.closest('.cart-remove-btn')) {
                    app.cart.remove(t.closest('.cart-remove-btn').dataset.id);
                    app.cart.render();
                }
                if (t.classList.contains('btn-checkout-modal')) {
                    const qty = parseInt(document.getElementById('modal-quantity-value').innerText);
                    app.cart.add(app.state.currentProduct, qty);
                    app.modal.close();
                    app.nav.showView('view-checkout');
                    app.cart.renderCheckout();
                }
                if (t.classList.contains('btn-checkout-page')) {
                    app.nav.showView('view-checkout');
                    app.cart.renderCheckout();
                }
                if (t.classList.contains('btn-place-order')) app.cart.confirmOrder();
                if (t.classList.contains('btn-back-main')) window.location.reload();

                // TABS
                if (t.classList.contains('purchase-tab')) app.nav.showPurchases(t.dataset.tab);

                // PROFILE SAVE
                if (t.classList.contains('btn-save-profile')) {
                    e.preventDefault();
                    fetch('/customer/profile', { 
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({
                            name: document.getElementById('prof-name').value,
                            email: document.getElementById('prof-email').value,
                            phone: document.getElementById('prof-phone').value,
                            address: document.getElementById('prof-address').value
                        })
                    }).then(res => res.json()).then(d => {
                        alert(d.message);
                        window.location.reload();
                    }).catch(()=>alert('Error saving profile'));
                }

                // REQUEST
                if (t.classList.contains('btn-submit-request')) {
                    e.preventDefault();
                    const desc = document.getElementById('request-description').value;
                    const budget = document.getElementById('request-budget').value;
                    const contact = document.getElementById('request-contact').value;

                    if(!desc.trim()) return alert('Please describe your arrangement.');

                    fetch('/customer/request', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ description: desc, budget: budget, contact_number: contact })
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);
                        setTimeout(() => app.nav.showView('view-dashboard'), 1000);
                    })
                    .catch(() => alert('Network Error'));
                }

                // TOGGLE FAVORITE
                if (t.closest('.fav-btn')) {
                    const btn = t.closest('.fav-btn');
                    const id = parseInt(btn.dataset.id);
                    const existing = app.state.favorites.find(f => f.id === id);
                    if (existing) {
                        app.state.favorites = app.state.favorites.filter(f => f.id !== id);
                        btn.querySelector('i').className = 'fa-regular text-gray-400 fa-heart';
                    } else {
                        const p = app.state.products.find(x => x.id === id);
                        if (p) {
                            app.state.favorites.push(p);
                            btn.querySelector('i').className = 'fa-solid text-red-500 fa-heart';
                        }
                    }
                    app.save();
                }
            });
        }
    };

    window.app = app;
    app.init();
});