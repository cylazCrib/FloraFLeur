document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const app = {
        state: {
            products: [], orders: [],
            cart: JSON.parse(localStorage.getItem('flora_cart')) || [],
            favorites: JSON.parse(localStorage.getItem('flora_favs')) || [],
            paymentMethod: 'Cash On Delivery',
            requests: [],
            shops: [],
            currentProduct: null,
            currentShop: null
        },

        init() {
            const prodEl = document.getElementById('db-products-payload');
            const ordEl = document.getElementById('db-orders-payload');
            const reqEl = document.getElementById('db-requests-payload');
            const shopsEl = document.getElementById('db-shops-payload');

            if(prodEl) try{ this.state.products = JSON.parse(prodEl.dataset.products); } catch(e){}
            if(ordEl) try{ this.state.orders = JSON.parse(ordEl.dataset.orders); } catch(e){}
            if(reqEl) try{ this.state.requests = JSON.parse(reqEl.dataset.requests); } catch(e){}
            if(shopsEl) try{ this.state.shops = JSON.parse(shopsEl.dataset.shops); } catch(e){}

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
            if(badge) { badge.innerText = count; badge.style.opacity = count > 0 ? 1 : 0; }
        },

        nav: {
            init() { this.showView('view-dashboard'); },
            showView(target) {
                document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.app-container').forEach(el => el.classList.remove('active'));
                const container = document.getElementById('app-container-dashboard');
                if(container) container.classList.add('active');
                const page = document.getElementById(target);
                if(page) { page.classList.remove('hidden'); page.classList.add('active'); }
                const bg = document.getElementById('bg-image-container');
                if(bg) bg.style.opacity = (target === 'view-dashboard') ? '1' : '0.2';
                
                // Handle custom request view - show shop info
                if (target === 'view-request') {
                    const shopBadge = document.getElementById('shop-info-badge');
                    const noShopWarning = document.getElementById('no-shop-warning');
                    const form = document.getElementById('custom-request-form');
                    
                    if (app.state.currentShop && app.state.currentShop.id) {
                        document.getElementById('selected-shop-name').textContent = app.state.currentShop.name;
                        shopBadge.style.display = 'block';
                        noShopWarning.style.display = 'none';
                        form.style.display = 'block';
                    } else {
                        shopBadge.style.display = 'none';
                        noShopWarning.style.display = 'block';
                        form.style.display = 'none';
                    }
                }
                
                window.scrollTo(0,0);
            },
            showPurchases(tab) {
                this.showView('view-purchases');
                document.querySelectorAll('.purchase-tab').forEach(t => {
                    t.classList.remove('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                    t.classList.add('border-transparent', 'text-gray-500');
                    if(t.dataset.tab === tab) { t.classList.add('active', 'border-[#86A873]', 'text-[#4A4A3A]'); t.classList.remove('border-transparent', 'text-gray-500'); }
                });
                const container = document.getElementById('purchases-list');
                container.innerHTML = '';
                
                if (tab === 'requests') {
                    if (!app.state.requests || app.state.requests.length === 0) { container.innerHTML = `<div class="text-center py-10 text-gray-400">No requests found.</div>`; return; }
                    app.state.requests.forEach(r => {
                        let statusColor = 'text-gray-500';
                        if(r.status==='reviewing' || r.status==='in_progress') statusColor='text-blue-600';
                        if(r.status==='approved' || r.status==='completed') statusColor='text-green-600';
                        if(r.status==='rejected') statusColor='text-red-600';
                        
                        let quoteSection = '';
                        if (r.vendor_quote) {
                            quoteSection = `<div style="margin-top: 1rem; padding: 1rem; background-color: #f0f8ff; border-left: 3px solid #0066cc; border-radius: 4px;"><strong>Vendor Quote:</strong> <span style="font-size: 1.2rem; color: #0066cc; font-weight: bold;">₱${parseFloat(r.vendor_quote).toFixed(2)}</span></div>`;
                        }
                        
                        let receiptBtn = `<button class="view-receipt-btn text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded border border-gray-300 ml-2" data-type="request" data-id="${r.id}"><i class="fa-solid fa-receipt"></i> View</button>`;
                        container.innerHTML += `<div class="bg-white p-6 rounded-lg shadow mb-4 text-gray-800 border-l-4 ${statusColor.replace('text-', 'border-')}"><div class="flex justify-between border-b pb-2 mb-2"><span class="font-bold">Request #${r.id}</span><span class="text-sm">${r.date}</span></div><div class="mb-2 italic text-sm">"${r.description}"</div><div class="flex justify-between font-bold border-t pt-2 items-center"><span>Budget: ${r.budget ? '₱'+parseFloat(r.budget).toLocaleString() : '-'}</span><div class="flex items-center gap-2"><span class="text-xs uppercase font-bold ${statusColor} px-2 py-1 bg-gray-100 rounded">${r.status}</span>${receiptBtn}</div></div>${quoteSection}</div>`;
                    });
                    return;
                }

                const filtered = app.state.orders.filter(o => {
                    if (tab === 'completed') return o.status === 'Delivered' || o.status === 'Completed';
                    if (tab === 'to-ship') return o.status !== 'Delivered' && o.status !== 'Completed' && o.status !== 'Canceled';
                    return o.status === tab;
                });
                
                if(filtered.length === 0) { container.innerHTML = `<div class="text-center py-10 text-gray-400">No orders found.</div>`; return; }
                
                filtered.forEach(o => {
                    const itemsHtml = o.items.map(i => `<div class="flex justify-between text-sm text-gray-600"><span>${i.qty}x ${i.name}</span><span>₱${parseFloat(i.price).toLocaleString()}</span></div>`).join('');
                    let receiptBtn = `<button class="view-receipt-btn text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded border border-gray-300 ml-2" data-type="order" data-id="${o.id}"><i class="fa-solid fa-receipt"></i> Receipt</button>`;
                    container.innerHTML += `<div class="bg-white p-6 rounded-lg shadow mb-4 text-gray-800"><div class="flex justify-between border-b pb-2 mb-2"><span class="font-bold">Order #${o.id}</span><span class="text-sm text-gray-500">${o.date}</span></div><div class="mb-3 space-y-1 pl-2 border-l-2 border-gray-100">${itemsHtml}</div><div class="flex justify-between font-bold border-t pt-2 items-center"><span>Total: ₱${parseFloat(o.total).toLocaleString()}</span><div class="flex items-center"><span class="text-xs uppercase font-bold text-[#86A873] px-2 py-1 bg-green-50 rounded">${o.status}</span>${receiptBtn}</div></div>${o.driver ? `<div class="mt-2 text-xs text-blue-600"><i class="fa-solid fa-truck"></i> Driver: ${o.driver}</div>` : ''}</div>`;
                });
            }
        },

        products: {
            renderFavorites() {
                const grid = document.getElementById('favorites-grid');
                grid.innerHTML = '';
                const favs = app.state.favorites;
                if (favs.length === 0) { grid.innerHTML = '<div class="col-span-full text-center py-20 text-white"><i class="fa-regular fa-heart text-6xl mb-4 opacity-50"></i><p class="text-xl">No favorites yet.</p><button class="mt-4 underline nav-link" data-target="view-dashboard">Go Shopping</button></div>'; return; }
                favs.forEach(p => {
                    grid.innerHTML += `<div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all relative"><button class="fav-btn absolute top-3 right-3 z-10 bg-white/80 p-2 rounded-full shadow hover:bg-white transition" data-id="${p.id}"><i class="fa-solid text-red-500 fa-heart"></i></button><div class="h-64 overflow-hidden cursor-pointer open-product-modal relative" data-id="${p.id}"><img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"></div><div class="p-4 text-center"><h3 class="font-rosarivo text-lg text-[#4A4A3A] mb-1">${p.name}</h3><p class="text-[#86A873] font-bold text-lg">₱${parseFloat(p.price).toLocaleString()}</p><button class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded mt-3 hover:bg-[#86A873] transition-colors open-product-modal" data-id="${p.id}">VIEW</button></div></div>`;
                });
            },
            render(filterType, filterValue) {
                const titles = { 'bouquet':'Bouquets', 'basket':'Baskets', 'box':'Boxes', 'standee':'Standees', 'potted':'Plants', 'valentines':"Valentine's", 'wedding':'Wedding', 'all':'All Collection' };
                const titleEl = document.getElementById('product-category-title');
                if(titleEl) titleEl.innerText = titles[filterValue] || filterValue.charAt(0).toUpperCase() + filterValue.slice(1);
                const grid = document.getElementById('products-grid');
                grid.innerHTML = '';
                const items = app.state.products.filter(p => {
                    if (filterType === 'all') return true;
                    if (filterType === 'occasion') return p.occasion === filterValue;
                    if (filterType === 'category') return p.category === filterValue;
                });
                if (items.length === 0) { grid.innerHTML = '<div class="col-span-full text-center py-10 text-gray-400">No products found for this category.</div>'; return; }
                items.forEach(p => {
                    const isFav = app.state.favorites.some(f => f.id === p.id);
                    const heartClass = isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-400';
                    grid.innerHTML += `<div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all relative"><button class="fav-btn absolute top-3 right-3 z-10 bg-white/80 p-2 rounded-full shadow hover:bg-white transition" data-id="${p.id}"><i class="${heartClass} fa-heart"></i></button><div class="h-64 overflow-hidden cursor-pointer open-product-modal relative" data-id="${p.id}"><img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"><div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div></div><div class="p-4 text-center"><h3 class="font-rosarivo text-lg text-[#4A4A3A] mb-1">${p.name}</h3><p class="text-[#86A873] font-bold text-lg">₱${parseFloat(p.price).toLocaleString()}</p><button class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded mt-3 hover:bg-[#86A873] transition-colors open-product-modal" data-id="${p.id}">VIEW DETAILS</button></div></div>`;
                });
            }
        },

        shops: {
            render() {
                const container = document.getElementById('shops-grid');
                container.innerHTML = '';
                if (!app.state.shops || app.state.shops.length === 0) {
                    container.innerHTML = '<div class="text-center py-10 text-gray-400 col-span-full">No shops available.</div>';
                    return;
                }
                app.state.shops.forEach(shop => {
                    const productsCount = shop.products ? shop.products.length : 0;
                    container.innerHTML += `
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all p-6 cursor-pointer border-l-4 border-[#86A873] shop-card" data-shop-id="${shop.id}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="font-rosarivo text-2xl text-[#4A4A3A] mb-1">${shop.name}</h3>
                                    <p class="text-gray-600 text-sm">${shop.description || 'Premium flower arrangements'}</p>
                                </div>
                                <span class="bg-[#86A873] text-white px-3 py-1 rounded-full text-sm font-bold">${productsCount} items</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-700 mb-4">
                                <div><i class="fa-solid fa-location-dot text-[#86A873] mr-2"></i>${shop.address || 'Location TBA'}</div>
                                <div><i class="fa-solid fa-phone text-[#86A873] mr-2"></i>${shop.phone || 'Contact TBA'}</div>
                                <div><i class="fa-solid fa-envelope text-[#86A873] mr-2"></i>${shop.email || 'Email TBA'}</div>
                            </div>
                            <div class="shop-products-preview grid grid-cols-3 gap-2 max-h-0 overflow-hidden transition-all duration-300">
                                ${shop.products?.slice(0, 3).map(p => `<div class="h-24 rounded overflow-hidden bg-gray-200"><img src="${p.image}" class="w-full h-full object-cover"></div>`).join('') || ''}
                            </div>
                            <button class="view-shop-products-btn w-full bg-[#4A4A3A] text-white py-2 rounded mt-4 hover:bg-[#86A873] transition-colors font-bold" data-shop-id="${shop.id}">View Products</button>
                        </div>
                    `;
                });
                this.attachShopListeners();
            },

            attachShopListeners() {
                document.querySelectorAll('.view-shop-products-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const shopId = parseInt(btn.dataset.shopId);
                        const shop = app.state.shops.find(s => s.id === shopId);
                        if (shop) {
                            app.state.currentShop = shop;
                            app.products.render('all', shop.name);
                            // Inject shop products into the products list
                            const grid = document.getElementById('products-grid');
                            grid.innerHTML = '';
                            if (shop.products.length === 0) {
                                grid.innerHTML = '<div class="col-span-full text-center py-10 text-gray-400">This shop has no products available.</div>';
                            } else {
                                shop.products.forEach(p => {
                                    const isFav = app.state.favorites.some(f => f.id === p.id);
                                    const heartClass = isFav ? 'fa-solid text-red-500' : 'fa-regular text-gray-400';
                                    grid.innerHTML += `<div class="bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all relative"><button class="fav-btn absolute top-3 right-3 z-10 bg-white/80 p-2 rounded-full shadow hover:bg-white transition" data-id="${p.id}"><i class="${heartClass} fa-heart"></i></button><div class="h-64 overflow-hidden cursor-pointer open-product-modal relative" data-id="${p.id}"><img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"><div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div></div><div class="p-4 text-center"><h3 class="font-rosarivo text-lg text-[#4A4A3A] mb-1">${p.name}</h3><p class="text-[#86A873] font-bold text-lg">₱${parseFloat(p.price).toLocaleString()}</p><button class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded mt-3 hover:bg-[#86A873] transition-colors open-product-modal" data-id="${p.id}">VIEW DETAILS</button></div></div>`;
                                });
                            }
                            app.nav.showView('view-products');
                        }
                    });
                });
            }
        },

        cart: {
            add(p, qty) {
                const exist = app.state.cart.find(i => i.id == p.id);
                if(exist) exist.qty += qty; else app.state.cart.push({...p, qty});
                app.save(); alert('Added to Cart');
            },
            remove(id) { app.state.cart = app.state.cart.filter(i => i.id != id); app.save(); },
            render() {
                const tbody = document.getElementById('cart-items-container');
                tbody.innerHTML = '';
                let total = 0;
                if(app.state.cart.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-400">Your cart is empty.</td></tr>'; } 
                else {
                    app.state.cart.forEach(item => {
                        total += item.price * item.qty;
                        tbody.innerHTML += `<tr class="border-b hover:bg-gray-50"><td class="px-6 py-4 flex gap-4 items-center"><img src="${item.image}" class="w-12 h-12 rounded object-cover shadow-sm"><span class="font-medium text-gray-800">${item.name}</span></td><td class="px-6 py-4 text-gray-600">₱${item.price.toLocaleString()}</td><td class="px-6 py-4 font-bold">${item.qty}</td><td class="px-6 py-4 font-bold text-[#86A873]">₱${(item.price*item.qty).toLocaleString()}</td><td class="px-6 py-4"><button class="text-red-400 hover:text-red-600 cart-remove-btn px-2 py-1 rounded hover:bg-red-50" data-id="${item.id}"><i class="fa-solid fa-trash"></i></button></td></tr>`;
                    });
                }
                document.getElementById('cart-total').innerText = '₱'+total.toLocaleString();
            },
            renderCheckout() {
                 const div = document.getElementById('checkout-cart-items');
                 div.innerHTML = '';
                 let total = 0;
                 if(app.state.cart.length === 0) { div.innerHTML = '<p class="text-gray-400 text-center">No items to checkout.</p>'; } 
                 else {
                     app.state.cart.forEach(item => {
                         total += item.price * item.qty;
                         div.innerHTML += `<div class="flex justify-between text-sm py-2 border-b border-dashed border-gray-200"><span class="text-gray-600">${item.qty}x ${item.name}</span><span class="font-bold text-gray-800">₱${(item.price*item.qty).toLocaleString()}</span></div>`;
                     });
                 }
                 document.getElementById('checkout-total').innerText = '₱'+total.toLocaleString();
            },
            
            // [UPDATED] Uses Classes instead of Inline Styles
            confirmOrder() {
                if(app.state.cart.length === 0) return alert('Your cart is empty.');
                const method = app.state.paymentMethod;

                if (method === 'Cash On Delivery') {
                    if(confirm("Confirm order via Cash on Delivery?")) {
                        app.cart.submitOrder(null);
                    }
                } 
                else {
                    // Setup Modal Content based on method
                    if (method === 'E-Wallet') {
                        document.getElementById('pay-modal-title').innerText = 'E-Wallet Payment';
                        document.getElementById('pay-ewallet-content').classList.remove('hidden');
                        document.getElementById('pay-card-content').classList.add('hidden');
                    } else {
                        document.getElementById('pay-modal-title').innerText = 'Card Payment';
                        document.getElementById('pay-ewallet-content').classList.add('hidden');
                        document.getElementById('pay-card-content').classList.remove('hidden');
                    }
                    
                    // Show Modal using Classes (Fixes the Close Bug)
                    const modal = document.getElementById('payment-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            },

            submitOrder(refNo) {
                const items = app.state.cart.map(i => ({ id: i.id, qty: i.qty }));
                const btn = document.querySelector('.btn-place-order');
                const modalBtn = document.getElementById('btn-confirm-payment');
                
                if(btn) { btn.innerText = 'Processing...'; btn.disabled = true; }
                if(modalBtn) { modalBtn.innerText = 'Processing...'; modalBtn.disabled = true; }

                fetch('/customer/order', { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                    body: JSON.stringify({ 
                        items, 
                        payment_method: app.state.paymentMethod,
                        payment_reference: refNo 
                    }) 
                })
                .then(res => res.json())
                .then(data => {
                    if(data.message) { 
                        app.state.cart = []; app.save(); 
                        app.modal.close(); // Use the safe close function
                        document.getElementById('thank-you-modal').style.display = 'flex'; 
                    }
                })
                .catch(err => { alert('Order failed. Please try again.'); console.error(err); })
                .finally(() => { 
                    if(btn) { btn.innerText = 'Place Order'; btn.disabled = false; }
                    if(modalBtn) { modalBtn.innerText = 'Confirm Payment'; modalBtn.disabled = false; }
                });
            }
        },

        receipt: {
            open(id, type) {
                let data = {};
                let title = 'Official Receipt';
                
                if (type === 'request') {
                    const req = app.state.requests.find(r => String(r.id) === String(id));
                    if (!req) return;
                    
                    // Use vendor quote if approved, otherwise use budget
                    const finalPrice = (req.vendor_quote && req.status === 'approved') ? req.vendor_quote : req.budget;
                    data = { id: 'REQ-' + req.id, date: req.date, total: finalPrice, items: [{ qty: 1, name: 'Custom Arrangement - ' + (req.occasion || 'Special'), price: finalPrice }], vendor_quote: req.vendor_quote, budget: req.budget, status: req.status };
                    if (req.status !== 'Delivered' && req.status !== 'Completed') title = 'Acknowledgement Receipt';
                } else {
                    const order = app.state.orders.find(o => String(o.id) === String(id));
                    if (!order) return;
                    data = { id: '#' + order.id, date: order.date, total: order.total, items: order.items };
                    if (order.status !== 'Delivered' && order.status !== 'Completed') title = 'Acknowledgement Receipt';
                }

                const content = document.getElementById('receipt-content');
                content.querySelector('p.text-xs.text-black').innerText = title;
                document.getElementById('rec-id').innerText = data.id;
                document.getElementById('rec-date').innerText = data.date;
                document.getElementById('rec-total').innerText = '₱' + parseFloat(data.total).toLocaleString();
                const tbody = document.getElementById('rec-items');
                tbody.innerHTML = '';
                data.items.forEach(i => { tbody.innerHTML += `<tr class="text-xs"><td class="py-1 text-black"><span class="font-bold">${i.qty}x</span> ${i.name}</td><td class="text-right py-1 text-black">₱${(i.price * i.qty).toLocaleString()}</td></tr>`; });
                
                // Show vendor quote information if it's a request with a quote
                const quoteInfoEl = document.getElementById('rec-quote-info');
                if (quoteInfoEl && type === 'request') {
                    if (data.vendor_quote) {
                        quoteInfoEl.innerHTML = `
                            <tr style="border-top: 1px solid #ddd;">
                                <td colspan="2" style="padding: 0.5rem 0;"></td>
                            </tr>
                            <tr class="text-xs">
                                <td class="py-1 text-black"><strong>Original Budget:</strong></td>
                                <td class="text-right py-1 text-gray-600">₱${parseFloat(data.budget).toLocaleString()}</td>
                            </tr>
                            <tr class="text-xs">
                                <td class="py-1 text-black"><strong style="color: #0066cc;">Vendor Quote:</strong></td>
                                <td class="text-right py-1" style="color: #0066cc; font-weight: bold;"><span style="font-size: 1.1rem;">₱${parseFloat(data.vendor_quote).toFixed(2)}</span></td>
                            </tr>
                            <tr class="text-xs" style="${data.status === 'approved' ? 'background-color: #e8f5e9;' : 'background-color: #fff3e0;'}">
                                <td class="py-1 text-black"><strong>Status:</strong></td>
                                <td class="text-right py-1"><span style="padding: 3px 8px; border-radius: 3px; font-weight: bold; ${data.status === 'approved' ? 'background-color: #4CAF50; color: white;' : 'background-color: #FFC107; color: #000;'}">${data.status.toUpperCase()}</span></td>
                            </tr>
                        `;
                    } else {
                        quoteInfoEl.innerHTML = '';
                    }
                } else if (quoteInfoEl) {
                    quoteInfoEl.innerHTML = '';
                }
                
                const modal = document.getElementById('receipt-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
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
                const modal = document.getElementById('product-modal');
                modal.classList.remove('hidden'); modal.classList.add('flex');
            },
            close() { 
                // [CRITICAL FIX] This now wipes inline styles to fix the "Close Button Not Working" bug
                document.querySelectorAll('.app-container.fixed').forEach(m => { 
                    m.classList.add('hidden'); 
                    m.classList.remove('flex'); 
                    m.style.display = ''; // Removes inline 'display: flex'
                }); 
            },
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
                if (t.closest('.nav-link')) { e.preventDefault(); const target = t.closest('.nav-link').dataset.target; app.nav.showView(target); if(target==='view-cart') app.cart.render(); if(target==='view-purchases') app.nav.showPurchases('to-ship'); if(target==='view-favorites') app.products.renderFavorites(); if(target==='view-shops') app.shops.render(); }
                if (t.closest('.filter-btn')) { e.preventDefault(); const btn = t.closest('.filter-btn'); app.products.render(btn.dataset.type, btn.dataset.value); app.nav.showView('view-products'); }
                if (t.closest('.open-product-modal')) app.modal.open(t.closest('.open-product-modal').dataset.id);
                if (t.closest('.modal-close-btn')) app.modal.close();
                if (t.classList.contains('quantity-btn-plus')) app.modal.adjustQty(1);
                if (t.classList.contains('quantity-btn-minus')) app.modal.adjustQty(-1);
                if (t.classList.contains('btn-add-cart')) { app.cart.add(app.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText)); app.modal.close(); }
                if (t.closest('.cart-remove-btn')) { app.cart.remove(t.closest('.cart-remove-btn').dataset.id); app.cart.render(); }
                if (t.classList.contains('btn-checkout-modal')) { app.cart.add(app.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText)); app.modal.close(); app.nav.showView('view-checkout'); app.cart.renderCheckout(); }
                if (t.classList.contains('btn-checkout-page')) { app.nav.showView('view-checkout'); app.cart.renderCheckout(); }
                if (t.classList.contains('btn-place-order')) app.cart.confirmOrder();
                if (t.classList.contains('btn-back-main')) window.location.reload();
                if (t.classList.contains('payment-btn')) { document.querySelectorAll('.payment-btn').forEach(b => { b.classList.remove('bg-[#86A873]', 'text-white', 'selected', 'border-gray-300'); b.classList.add('border-gray-300', 'text-gray-800'); }); t.classList.add('bg-[#86A873]', 'text-white', 'selected'); t.classList.remove('border-gray-300', 'text-gray-800'); app.state.paymentMethod = t.dataset.method; }
                if (t.classList.contains('purchase-tab')) app.nav.showPurchases(t.dataset.tab);
                if (t.closest('.view-receipt-btn')) { const btn = t.closest('.view-receipt-btn'); app.receipt.open(btn.dataset.id, btn.dataset.type); }
                if (t.classList.contains('btn-save-profile')) { e.preventDefault(); fetch('/customer/profile', { method: 'PATCH', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ name: document.getElementById('prof-name').value, email: document.getElementById('prof-email').value, phone: document.getElementById('prof-phone').value, address: document.getElementById('prof-address').value }) }).then(res => res.json()).then(d => { alert(d.message); window.location.reload(); }).catch(()=>alert('Error saving profile')); }
                if (t.classList.contains('btn-submit-request')) { 
                    e.preventDefault();
                    
                    if (!app.state.currentShop || !app.state.currentShop.id) {
                        return alert('Please select a shop first from the All Shops section.');
                    }
                    
                    const desc = document.getElementById('request-description').value; 
                    const occasion = document.getElementById('request-occasion').value;
                    const dateNeeded = document.getElementById('request-date-needed').value;
                    const budget = document.getElementById('request-budget').value; 
                    const color = document.getElementById('request-color').value;
                    const contact = document.getElementById('request-contact').value;
                    const refLink = document.getElementById('request-reference-link').value;
                    
                    if(!desc.trim()) return alert('Please describe your arrangement.');
                    if(!dateNeeded) return alert('Please specify when you need it.');
                    if(!contact.trim()) return alert('Please provide a contact number.');
                    
                    fetch('/customer/request', { 
                        method: 'POST', 
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, 
                        body: JSON.stringify({ 
                            shop_id: app.state.currentShop.id,
                            description: desc, 
                            occasion: occasion,
                            date_needed: dateNeeded,
                            budget: budget, 
                            color_preference: color,
                            contact_number: contact,
                            reference_image_url: refLink
                        }) 
                    }).then(res => res.json()).then(data => { alert(data.message); setTimeout(() => app.nav.showView('view-dashboard'), 1000); }).catch(() => alert('Network Error')); 
                }
                if (t.closest('.fav-btn')) { const btn = t.closest('.fav-btn'); const id = parseInt(btn.dataset.id); const existing = app.state.favorites.find(f => f.id === id); if (existing) { app.state.favorites = app.state.favorites.filter(f => f.id !== id); btn.querySelector('i').className = 'fa-regular text-gray-400 fa-heart'; } else { const p = app.state.products.find(x => x.id === id); if (p) { app.state.favorites.push(p); btn.querySelector('i').className = 'fa-solid text-red-500 fa-heart'; } } app.save(); if(!document.getElementById('view-favorites').classList.contains('hidden')) app.products.renderFavorites(); }

                if (t.id === 'btn-confirm-payment') {
                    let ref = null;
                    if (app.state.paymentMethod === 'E-Wallet') {
                        ref = document.getElementById('pay-ref-number').value;
                        if(!ref) return alert('Please enter the Reference Number.');
                        ref = 'GCash Ref: ' + ref;
                    } else if (app.state.paymentMethod === 'Card') {
                        ref = 'Card Trans ID: ' + Math.floor(Math.random() * 10000000); 
                    }
                    app.cart.submitOrder(ref);
                }
            });
        }
    };

    window.app = app;
    app.init();
});