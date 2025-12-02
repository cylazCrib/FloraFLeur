/**
 * Flora Fleur Customer Dashboard Logic
 * Handles Navigation, Cart, Favorites, and Mock Backend Logic
 */

window.app = {
    state: {
        cart: [],
        favorites: [],
        orders: [], 
        addresses: [],
        deliveryMethod: 'delivery', 
        selectedAddress: null,
        paymentMethod: null,
        products: [
             // --- Bouquets ---
            { id: 1, name: 'Red Roses Bouquet', price: 1500, category: 'bouquet', occasion: 'valentines', image: 'https://i.pinimg.com/736x/20/d9/75/20d975ae5d4f33d0caed1a0dad56b4a1.jpg', description: 'A timeless classic.' },
            { id: 11, name: 'Elegant White Roses', price: 2500, category: 'bouquet', occasion: 'wedding', image: 'https://i.pinimg.com/736x/fb/b2/ff/fbb2ff7675391e0ec79d8b1e94f9a364.jpg', description: 'Pure and pristine white roses.' },
            { id: 12, name: 'Classic Bridal Bouquet', price: 2800, category: 'bouquet', occasion: 'wedding', image: 'https://i.pinimg.com/736x/8d/ec/ea/8decea6c3bfa62dc7abfc85d25f8fab7.jpg', description: 'Sophisticated mix of white roses.' },
            { id: 17, name: 'Deep Red Romance', price: 1800, category: 'bouquet', occasion: 'valentines', image: 'https://i.pinimg.com/736x/2b/ac/5f/2bac5f79e5d7a87a2e4c0a072998ffc7.jpg', description: 'Intense crimson roses.' },
            { id: 18, name: 'Pink & White Delight', price: 1600, category: 'bouquet', occasion: 'valentines', image: 'https://i.pinimg.com/1200x/66/56/5f/66565f8a5b2b1e9ccccf7e33fe6027ea.jpg', description: 'Sweet pink and white blooms.' },
            { id: 19, name: 'Sunflower Surprise', price: 1400, category: 'bouquet', occasion: 'valentines', image: 'https://i.pinimg.com/1200x/d4/a7/7c/d4a77c27bf85182d423fa18b06e47784.jpg', description: 'Bright sunflowers mixed with berries.' },
            { id: 20, name: 'Lavender Dreams', price: 1700, category: 'bouquet', occasion: 'valentines', image: 'https://i.pinimg.com/736x/93/70/e3/9370e3f359041ff4e3ae470e15e8a4cd.jpg', description: 'Fragrant purple hues.' },

            // --- Standees ---
            { id: 2, name: 'Grand Opening Standee', price: 3500, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/1200x/43/b3/4f/43b34f35e4fe86163f3a80e32213212c.jpg', description: 'Make a bold statement.' },
            { id: 6, name: 'Wedding Arch', price: 15000, category: 'standee', occasion: 'wedding', image: 'https://i.pinimg.com/1200x/85/db/85/85db85f46627b46a16c2cd697a72cc21.jpg', description: 'Magical backdrop.' },
            { id: 16, name: 'Boho Wedding Backdrop', price: 8000, category: 'standee', occasion: 'wedding', image: 'https://i.pinimg.com/1200x/cf/23/7d/cf237d3df5e0ba7b0bcf0025a4ac1f9b.jpg', description: 'Free-spirited backdrop.' },
            { id: 26, name: 'Radiant Inaugural', price: 3800, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/1200x/f5/fc/b9/f5fcb970da51d5a2ed9b64514d232080.jpg', description: 'Striking display of orange heliconias.' },
            { id: 27, name: 'Golden Success', price: 4200, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/736x/14/3b/ca/143bca432537a2b32dbcc41fa57a4e2c.jpg', description: 'Tall yellow mums and golden palms.' },
            { id: 28, name: 'Modern Chic Standee', price: 3500, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/1200x/a2/6e/68/a26e68dfb26d0aaa83eea06f6114b558.jpg', description: 'Contemporary arrangement.' },
            { id: 29, name: 'Classic Congratulatory', price: 3000, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/736x/e7/73/35/e7733529f76975ae6e3eedde5aa229dc.jpg', description: 'Traditional red gerberas.' },
            { id: 30, name: 'Vibrant Victory', price: 3600, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/736x/53/e0/1b/53e01b75b570bf237edfbcfd0089ce0e.jpg', description: 'Explosion of color.' },
            { id: 31, name: 'Elegant Opening', price: 4000, category: 'standee', occasion: 'grand-opening', image: 'https://i.pinimg.com/736x/e9/df/b2/e9dfb27f2d71a28621145c692ae1ecd7.jpg', description: 'Sophisticated pink lilies.' },

            // --- Potted Plants ---
            { id: 3, name: 'Indoor Fern Basket', price: 850, category: 'basket', occasion: 'all', image: 'https://i.pinimg.com/736x/0e/24/00/0e2400b844843a0164813e65ced223d8.jpg', description: 'Lush Boston Fern.' },
            { id: 7, name: 'Exotic Caladium', price: 1100, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/736x/4f/82/43/4f824381bc0d0b2fc707c6f2266805fd.jpg', description: 'Heart-shaped leaves.' },
            { id: 8, name: 'Red Anthurium Pot', price: 1450, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/736x/3d/ec/b3/3decb3de528c52bb19d8dceb271ff4df.jpg', description: 'Bright waxy red blooms.' },
            { id: 9, name: 'Peace Lily Elegance', price: 1300, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/1200x/cb/58/91/cb5891bd9d2140345723840b7fa9908f.jpg', description: 'Elegant air-purifying plant.' },
            { id: 10, name: 'Burgundy Rubber Plant', price: 1850, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/736x/bc/8d/ca/bc8dcab126ff40e3f95964141633a7d1.jpg', description: 'Robust indoor tree.' },
            { id: 32, name: 'Monstera Deliciosa', price: 1600, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/1200x/7e/0b/34/7e0b34bd83b6be958b31f58d7e9cb91c.jpg', description: 'Iconic Swiss Cheese Plant.' },
            { id: 33, name: 'Trailing Golden Pothos', price: 950, category: 'potted', occasion: 'all', image: 'https://i.pinimg.com/736x/79/3c/2e/793c2e15c54f92d287e16910763e1913.jpg', description: 'Resilient fast-growing vine.' },

            // --- Flower Boxes ---
            { id: 21, name: 'Peach Hatbox', price: 2100, category: 'box', occasion: 'all', image: 'https://i.pinimg.com/1200x/85/77/1c/85771c431d647833304aa1099abe2f92.jpg', description: 'Warm peach roses.' },
            { id: 22, name: 'Blue Hydrangea Box', price: 2300, category: 'box', occasion: 'all', image: 'https://i.pinimg.com/736x/9f/f3/66/9ff3669cb70928b46566894308fbc109.jpg', description: 'Voluminous blue hydrangeas.' },
            { id: 23, name: 'Mixed Spring Box', price: 1950, category: 'box', occasion: 'all', image: 'https://i.pinimg.com/1200x/d6/f5/15/d6f5154991d9d7b9864241758107b22d.jpg', description: 'Vibrant explosion of colors.' },
            { id: 24, name: 'Elegant Black Box', price: 2600, category: 'box', occasion: 'all', image: 'https://i.pinimg.com/1200x/57/f9/6c/57f96c70d16614419bd08b2dd2ff930e.jpg', description: 'Premium red roses.' },
            { id: 25, name: 'Heart Shaped Rose Box', price: 2900, category: 'box', occasion: 'valentines', image: 'https://i.pinimg.com/1200x/bf/30/88/bf3088d7dbf1210d450bc9502b5be6a7.jpg', description: 'Romantic heart-shaped box.' }
        ],
        currentProduct: null,
        currentView: {
            filter: 'all',
            title: 'Collection'
        }
    },

    init() {
        const storedCart = localStorage.getItem('flora_cart');
        const storedFavs = localStorage.getItem('flora_favs');
        const storedOrders = localStorage.getItem('flora_orders');
        const storedAddresses = localStorage.getItem('flora_addresses');

        if (storedCart) this.state.cart = JSON.parse(storedCart);
        if (storedFavs) this.state.favorites = JSON.parse(storedFavs);
        if (storedOrders) this.state.orders = JSON.parse(storedOrders);
        if (storedAddresses) this.state.addresses = JSON.parse(storedAddresses);

        this.updateBadges();
        this.nav.init();
        this.account.renderAddresses();
        this.delivery.renderAddressSelect(); 
        
        // Listeners
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('product-modal').style.display = 'none';
                document.getElementById('thank-you-modal').style.display = 'none';
            });
        });

        // Product Modal
        const minusBtn = document.querySelector('.quantity-btn-minus');
        const plusBtn = document.querySelector('.quantity-btn-plus');
        if(minusBtn) minusBtn.addEventListener('click', () => this.modal.adjustQuantity(-1));
        if(plusBtn) plusBtn.addEventListener('click', () => this.modal.adjustQuantity(1));
        
        const modalAddToCart = document.querySelector('#product-modal button:first-of-type');
        if(modalAddToCart) {
            modalAddToCart.addEventListener('click', () => {
                 this.cart.add(this.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText));
                 document.getElementById('product-modal').style.display = 'none';
                 this.toast.show('Added to Cart');
            });
        }

        // Modal "Check Out" button -> Go to Checkout View
        const modalCheckout = document.querySelector('#product-modal .btn-checkout');
        if(modalCheckout) {
            modalCheckout.addEventListener('click', () => {
                 this.cart.add(this.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText));
                 document.getElementById('product-modal').style.display = 'none';
                 this.nav.showCheckout(); // Navigate to checkout
            });
        }
        
        const backMainBtn = document.querySelector('.btn-back-main');
        if(backMainBtn) {
            backMainBtn.addEventListener('click', () => {
                 document.getElementById('thank-you-modal').style.display = 'none';
                 this.nav.showDashboard();
            });
        }
    },

    // ... (save, updateBadges, toast logic remains same) ...
    save() {
        localStorage.setItem('flora_cart', JSON.stringify(this.state.cart));
        localStorage.setItem('flora_favs', JSON.stringify(this.state.favorites));
        localStorage.setItem('flora_orders', JSON.stringify(this.state.orders));
        localStorage.setItem('flora_addresses', JSON.stringify(this.state.addresses));
        this.updateBadges();
        this.cart.renderCheckout(); 
    },

    updateBadges() {
        const cartCount = this.state.cart.reduce((acc, item) => acc + item.qty, 0);
        const favCount = this.state.favorites.length;
        const cartBadge = document.getElementById('cart-badge');
        const favBadge = document.getElementById('fav-badge');

        if(cartBadge) {
            cartBadge.innerText = cartCount;
            cartBadge.style.opacity = cartCount > 0 ? '1' : '0';
        }
        if(favBadge) {
            favBadge.innerText = favCount;
            favBadge.style.opacity = favCount > 0 ? '1' : '0';
        }
    },

    toast: {
        show(message, type = 'success') {
            const toast = document.getElementById('order-toast');
            if(!toast) return;
            toast.querySelector('span').innerText = type === 'success' ? 'Success' : 'Note';
            toast.querySelector('p').innerText = message;
            toast.classList.remove('translate-x-[120%]');
            setTimeout(() => { toast.classList.add('translate-x-[120%]'); }, 3000);
        }
    },

    nav: {
        // Helper to hide all views and manage opacity
        hideAll() {
            document.querySelectorAll('.view-section').forEach(el => {
                el.classList.remove('active'); // Start fade out
                setTimeout(() => {
                    if (!el.classList.contains('active')) el.classList.add('hidden'); // Hide after fade out starts
                }, 300); // Match CSS transition time
                el.classList.add('hidden'); // Immediate hide for snappy feel, allow opacity to transition in next step
            });
            window.scrollTo(0, 0); 
            
            // Background Logic
            const bg = document.getElementById('bg-image-container');
            if (bg) bg.classList.add('bg-faded'); 
            document.getElementById('app-container-dashboard').classList.remove('bg-[#F5F5F0]'); 
        },

        // Helper to show a specific view with fade in
        showView(viewId) {
            this.hideAll();
            const view = document.getElementById(viewId);
            if(view) {
                view.classList.remove('hidden');
                // Small delay to allow browser to register 'display: block' before adding opacity class
                setTimeout(() => {
                    view.classList.add('active');
                }, 50);
            }
             document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
        },

        init() {
            // Default view
            const dash = document.getElementById('view-dashboard');
            if(dash) {
                dash.classList.remove('hidden');
                setTimeout(() => dash.classList.add('active'), 50);
                const bg = document.getElementById('bg-image-container');
                if (bg) bg.classList.remove('bg-faded'); // Full opacity for dashboard
            }
        },

        showDashboard(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const dash = document.getElementById('view-dashboard');
            dash.classList.remove('hidden');
            setTimeout(() => dash.classList.add('active'), 50);
            
            // Special background for dashboard
            const bg = document.getElementById('bg-image-container');
            if (bg) bg.classList.remove('bg-faded'); 
        },

        showCheckout(e) {
            if(e) e.preventDefault();
            if (window.app.state.cart.length === 0) {
                window.app.toast.show('Cart is empty', 'error');
                return;
            }
            this.showView('view-checkout');
            window.app.cart.renderCheckout();
        },

        filterProducts(filter, title) {
            window.app.state.currentView.filter = filter;
            window.app.state.currentView.title = title;

            this.showView('view-shop');
            
            document.getElementById('product-category-title').innerText = title;

            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';

            const products = filter === 'all' 
                ? window.app.state.products 
                : window.app.state.products.filter(p => p.category === filter || p.occasion === filter);

            products.forEach(p => {
                const isFav = window.app.favorites.has(p.id);
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300';
                card.innerHTML = `
                    <div class="relative h-64 overflow-hidden cursor-pointer" onclick="window.app.modal.open(${p.id})">
                        <img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div id="fav-btn-${p.id}" class="absolute bottom-2 right-2 bg-white/90 p-2 rounded-full shadow hover:bg-[#86A873] hover:text-white transition-colors cursor-pointer" onclick="event.stopPropagation(); window.app.favorites.toggle(${p.id})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${isFav ? 'text-red-500 fill-current' : 'text-gray-600'}" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                        <p class="text-gray-500 text-sm mb-3">₱${p.price.toLocaleString()}</p>
                        <button onclick="window.app.cart.addById(${p.id})" class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded hover:bg-[#86A873] transition-colors uppercase font-bold tracking-wider cursor-pointer">
                            Add to Cart
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        },

        showFavorites(e) {
            if(e) e.preventDefault();
            this.showView('view-favorites');
            window.app.favorites.render();
        },
        
        showAbout(e) {
            if(e) e.preventDefault();
            this.showView('view-about');
        },

        showAccount(e) {
             if(e) e.preventDefault();
            this.showView('view-account');
        },
        
        showRequest(e) {
            if(e) e.preventDefault();
            this.showView('view-request');
        },
        
        showPurchases(tab = 'to-ship') {
            this.showView('view-purchases');
            this.switchPurchaseTab(tab);
        },
        
        switchPurchaseTab(tab) {
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
            
            const filteredOrders = window.app.state.orders.filter(o => o.status === tab);
            
            if(filteredOrders.length === 0) {
                container.innerHTML = `<div class="text-center py-10 text-gray-400 italic bg-white/80 backdrop-blur-sm rounded-lg">No orders in this category.</div>`;
                return;
            }

            filteredOrders.forEach(order => {
                const div = document.createElement('div');
                div.className = 'bg-white p-6 rounded-lg shadow border border-gray-100';
                div.innerHTML = `
                    <div class="flex justify-between mb-4 border-b border-gray-100 pb-2">
                        <span class="font-bold text-[#4A4A3A]">Order #${order.id}</span>
                        <span class="text-sm text-gray-500">${order.date}</span>
                    </div>
                    <div class="space-y-2 mb-4">
                        ${order.items.map(item => `
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">${item.qty}x ${item.name}</span>
                                <span class="font-medium">₱${(item.price * item.qty).toLocaleString()}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-[#86A873] uppercase tracking-wide">${order.status.replace('-', ' ')}</span>
                        <span class="font-rosarivo text-xl">Total: ₱${order.total.toLocaleString()}</span>
                    </div>
                `;
                container.appendChild(div);
            });
        },
    },

    payment: {
        selectMethod(method) {
            window.app.state.paymentMethod = method;
            
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('border-[#86A873]', 'text-[#86A873]');
                btn.classList.add('border-gray-200');
            });
            document.getElementById(`pm-${method}`).classList.add('border-[#86A873]', 'text-[#86A873]');
            document.getElementById(`pm-${method}`).classList.remove('border-gray-200');

            document.getElementById('form-cod').classList.add('hidden');
            document.getElementById('form-ewallet').classList.add('hidden');
            document.getElementById('form-card').classList.add('hidden');
            
            if(method === 'cod') document.getElementById('form-cod').classList.remove('hidden');
            if(method === 'ewallet') document.getElementById('form-ewallet').classList.remove('hidden');
            if(method === 'card') document.getElementById('form-card').classList.remove('hidden');
        }
    },

    delivery: {
        toggleMethod(method) {
            window.app.state.deliveryMethod = method;
            
            const delBtn = document.getElementById('del-delivery');
            const pickBtn = document.getElementById('del-pickup');
            
            if (method === 'delivery') {
                delBtn.classList.add('bg-[#86A873]', 'text-white', 'border-[#86A873]');
                delBtn.classList.remove('text-gray-600', 'border-gray-200');
                pickBtn.classList.remove('bg-[#86A873]', 'text-white', 'border-[#86A873]');
                pickBtn.classList.add('text-gray-600', 'border-gray-200');
                
                document.getElementById('opt-delivery').classList.remove('hidden');
                document.getElementById('opt-pickup').classList.add('hidden');
            } else {
                pickBtn.classList.add('bg-[#86A873]', 'text-white', 'border-[#86A873]');
                pickBtn.classList.remove('text-gray-600', 'border-gray-200');
                delBtn.classList.remove('bg-[#86A873]', 'text-white', 'border-[#86A873]');
                delBtn.classList.add('text-gray-600', 'border-gray-200');
                
                document.getElementById('opt-delivery').classList.add('hidden');
                document.getElementById('opt-pickup').classList.remove('hidden');
            }
            
            window.app.cart.renderCheckout(); 
        },
        renderAddressSelect() {
            const select = document.getElementById('delivery-address-select');
            if(!select) return;
            select.innerHTML = '';
            
            if (window.app.state.addresses.length === 0) {
                const opt = document.createElement('option');
                opt.text = "No saved addresses";
                select.add(opt);
                select.disabled = true;
            } else {
                select.disabled = false;
                window.app.state.addresses.forEach(addr => {
                    const opt = document.createElement('option');
                    opt.value = addr.id;
                    opt.text = `${addr.label}`;
                    select.add(opt);
                });
            }
        }
    },

    modal: {
        open(productId) {
            const p = window.app.state.products.find(x => x.id === productId);
            if(!p) return;
            
            window.app.state.currentProduct = p;
            
            document.getElementById('modal-product-image').src = p.image;
            document.getElementById('modal-product-title').innerText = p.name;
            const descEl = document.getElementById('modal-product-description');
            if(descEl) descEl.innerText = p.description || 'No description available.';
            
            document.getElementById('modal-product-price').innerText = '₱' + p.price.toLocaleString();
            document.getElementById('modal-quantity-value').innerText = '1';
            document.getElementById('product-modal').style.display = 'flex';
        },
        adjustQuantity(delta) {
            const el = document.getElementById('modal-quantity-value');
            let val = parseInt(el.innerText) + delta;
            if(val < 1) val = 1;
            el.innerText = val;
        }
    },

    cart: {
        addById(id) {
            const p = window.app.state.products.find(x => x.id === id);
            if(p) this.add(p, 1);
        },
        add(product, qty) {
            const existing = window.app.state.cart.find(i => i.id === product.id);
            if(existing) {
                existing.qty += qty;
            } else {
                window.app.state.cart.push({ ...product, qty: qty });
            }
            window.app.save();
            window.app.toast.show(`${product.name} added to cart!`);
        },
        remove(id) {
            window.app.state.cart = window.app.state.cart.filter(i => i.id !== id);
            window.app.save();
            this.renderCheckout(); // Re-render checkout summary
        },
        updateQty(id, delta) {
            const item = window.app.state.cart.find(i => i.id === id);
            if(item) {
                item.qty += delta;
                if(item.qty <= 0) {
                     this.remove(id);
                } else {
                    window.app.save();
                }
            }
        },
        renderCheckout() {
            const container = document.getElementById('checkout-cart-items');
            if(!container) return;

            container.innerHTML = '';
            
            if(window.app.state.cart.length === 0) {
                container.innerHTML = `<p class="text-gray-400 text-center italic text-sm">Your cart is empty.</p>`;
                document.getElementById('checkout-subtotal').innerText = '₱0.00';
                document.getElementById('checkout-fee').innerText = '₱0.00';
                document.getElementById('checkout-total').innerText = '₱0.00';
                return;
            }

            let subtotal = 0;
            
            window.app.state.cart.forEach(item => {
                subtotal += item.price * item.qty;
                const div = document.createElement('div');
                div.className = 'flex gap-3 items-center bg-gray-50 p-2 rounded border border-gray-100';
                div.innerHTML = `
                    <img src="${item.image}" class="w-12 h-12 object-cover rounded">
                    <div class="flex-grow">
                        <h4 class="text-xs font-bold text-[#4A4A3A] line-clamp-1">${item.name}</h4>
                        <p class="text-xs text-gray-500">₱${item.price.toLocaleString()}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="window.app.cart.updateQty(${item.id}, -1)" class="text-gray-400 hover:text-[#4A4A3A] text-xs font-bold px-1">-</button>
                        <span class="text-xs font-bold w-3 text-center">${item.qty}</span>
                        <button onclick="window.app.cart.updateQty(${item.id}, 1)" class="text-gray-400 hover:text-[#4A4A3A] text-xs font-bold px-1">+</button>
                    </div>
                `;
                container.appendChild(div);
            });

            const fee = window.app.state.deliveryMethod === 'delivery' ? 150 : 0;
            document.getElementById('checkout-subtotal').innerText = '₱' + subtotal.toLocaleString();
            document.getElementById('checkout-fee').innerText = '₱' + fee.toLocaleString();
            document.getElementById('checkout-total').innerText = '₱' + (subtotal + fee).toLocaleString();
        },
        confirmOrder() {
            if(window.app.state.cart.length === 0) {
                alert('Your cart is empty!');
                return;
            }
            
            if(!window.app.state.paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            
            // Validate Address if delivery
            if (window.app.state.deliveryMethod === 'delivery' && window.app.state.addresses.length === 0) {
                alert('Please add a delivery address in your Account settings first.');
                return;
            }

            const total = window.app.state.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            const fee = window.app.state.deliveryMethod === 'delivery' ? 150 : 0;
            
            const newOrder = {
                id: 'ORD-' + Math.floor(Math.random() * 10000),
                status: 'to-ship',
                items: [...window.app.state.cart],
                total: total + fee,
                date: new Date().toLocaleDateString(),
                method: window.app.state.deliveryMethod,
                payment: window.app.state.paymentMethod
            };
            
            window.app.state.orders.push(newOrder);
            window.app.state.cart = []; 
            window.app.save();
            
            document.getElementById('thank-you-modal').style.display = 'flex';
        },
        
        // Legacy function support
        render() {
            // Do nothing or redirect
        }
    },

    // ... (favorites, account, request objects remain same) ...
    favorites: {
        has(id) {
            return window.app.state.favorites.some(i => i.id === id);
        },
        toggle(id) {
             if(this.has(id)) {
                window.app.state.favorites = window.app.state.favorites.filter(i => i.id !== id);
                window.app.toast.show('Removed from Favorites');
            } else {
                const p = window.app.state.products.find(x => x.id === id);
                if(p) {
                    window.app.state.favorites.push(p);
                    window.app.toast.show('Added to Favorites');
                }
            }
            window.app.save();

            const favView = document.getElementById('view-favorites');
            if (!favView.classList.contains('hidden')) {
                this.render();
                return; 
            }
            
            // Also update hearts in grid
             const btn = document.getElementById(`fav-btn-${id}`);
            if (btn) {
                const svg = btn.querySelector('svg');
                if (svg) {
                    if (this.has(id)) {
                        svg.classList.remove('text-gray-600');
                        svg.classList.add('text-red-500', 'fill-current');
                    } else {
                        svg.classList.remove('text-red-500', 'fill-current');
                        svg.classList.add('text-gray-600');
                    }
                }
            }
        },
        render() {
             const grid = document.getElementById('favorites-grid');
            grid.innerHTML = '';
            const msg = document.getElementById('no-favorites-msg');

            if(window.app.state.favorites.length === 0) {
                msg.classList.remove('hidden');
            } else {
                msg.classList.add('hidden');
                window.app.state.favorites.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-lg shadow-md overflow-hidden group relative';
                    card.innerHTML = `
                        <div class="h-64 overflow-hidden cursor-pointer" onclick="window.app.modal.open(${p.id})">
                            <img src="${p.image}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                            <p class="text-gray-500 text-sm mb-3">₱${p.price.toLocaleString()}</p>
                            <div class="flex gap-2">
                                <button onclick="window.app.cart.addById(${p.id})" class="flex-1 bg-[#4A4A3A] text-white text-xs py-2 rounded hover:bg-[#86A873] transition-colors uppercase font-bold tracking-wider cursor-pointer">
                                    Add to Cart
                                </button>
                                <button onclick="window.app.favorites.toggle(${p.id})" class="px-3 border border-gray-200 rounded hover:bg-red-50 text-red-500 cursor-pointer">
                                    ✕
                                </button>
                            </div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            }
        }
    },
    
    account: {
        toggleEdit() {
            const display = document.getElementById('account-display');
            const edit = document.getElementById('account-edit');
            
            if (display.classList.contains('hidden')) {
                display.classList.remove('hidden');
                edit.classList.add('hidden');
            } else {
                display.classList.add('hidden');
                edit.classList.remove('hidden');
            }
        },
        save() {
            const name = document.getElementById('edit-name').value;
            const email = document.getElementById('edit-email').value;
            
            if(name) document.getElementById('display-name').innerText = name;
            if(email) document.getElementById('display-email').innerText = email;
            
            window.app.toast.show('Profile updated successfully!');
            this.toggleEdit();
        },
        showAddAddress() {
             document.getElementById('address-form').classList.remove('hidden');
            document.getElementById('address-form-title').innerText = 'New Address';
            document.getElementById('addr-id').value = '';
            document.getElementById('addr-label').value = '';
            document.getElementById('addr-street').value = '';
            document.getElementById('addr-city').value = '';
            document.getElementById('addr-zip').value = '';
        },
        cancelAddress() {
            document.getElementById('address-form').classList.add('hidden');
        },
        saveAddress() {
             const id = document.getElementById('addr-id').value;
            const label = document.getElementById('addr-label').value;
            const street = document.getElementById('addr-street').value;
            const city = document.getElementById('addr-city').value;
            const zip = document.getElementById('addr-zip').value;

            if (!label || !street || !city) {
                alert('Please fill required fields');
                return;
            }

            const newAddr = {
                id: id || Date.now(),
                label,
                street,
                city,
                zip
            };

            if (id) {
                const index = window.app.state.addresses.findIndex(a => a.id == id);
                if (index !== -1) {
                    window.app.state.addresses[index] = newAddr;
                }
            } else {
                window.app.state.addresses.push(newAddr);
            }

            window.app.save();
            this.renderAddresses();
            this.cancelAddress();
            window.app.delivery.renderAddressSelect(); 
            window.app.toast.show('Address saved successfully!');
        },
        deleteAddress(id) {
            if(confirm('Delete this address?')) {
                window.app.state.addresses = window.app.state.addresses.filter(a => a.id != id);
                window.app.save();
                this.renderAddresses();
                window.app.delivery.renderAddressSelect();
            }
        },
        editAddress(id) {
             const addr = window.app.state.addresses.find(a => a.id == id);
            if(!addr) return;

            document.getElementById('addr-id').value = addr.id;
            document.getElementById('addr-label').value = addr.label;
            document.getElementById('addr-street').value = addr.street;
            document.getElementById('addr-city').value = addr.city;
            document.getElementById('addr-zip').value = addr.zip;

            document.getElementById('address-form').classList.remove('hidden');
            document.getElementById('address-form-title').innerText = 'Edit Address';
        },
        renderAddresses() {
            const container = document.getElementById('address-list');
            if(!container) return;
            
            container.innerHTML = '';
            
            if (window.app.state.addresses.length === 0) {
                container.innerHTML = `<p class="text-gray-400 italic text-sm text-center py-4">No addresses saved yet.</p>`;
                return;
            }

            window.app.state.addresses.forEach(addr => {
                const div = document.createElement('div');
                div.className = 'border border-gray-200 rounded-lg p-4 flex justify-between items-center bg-gray-50';
                div.innerHTML = `
                    <div>
                        <span class="text-xs font-bold text-[#86A873] uppercase tracking-wide block mb-1">${addr.label}</span>
                        <p class="text-sm text-gray-700">${addr.street}, ${addr.city} ${addr.zip}</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="app.account.editAddress(${addr.id})" class="text-gray-400 hover:text-[#4A4A3A]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button onclick="app.account.deleteAddress(${addr.id})" class="text-gray-400 hover:text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                `;
                container.appendChild(div);
            });
        }
    },
    
    request: {
         submit() {
            const desc = document.getElementById('request-description').value;
            if(!desc.trim()) {
                alert('Please describe your request.');
                return;
            }
            document.getElementById('request-description').value = '';
            window.app.toast.show('Request sent to florist!');
            setTimeout(() => window.app.nav.showDashboard(), 1500);
        },
        call() {
            window.location.href = 'tel:+1234567890';
        }
    }
};

// Start App
window.addEventListener('DOMContentLoaded', () => {
    window.app.init();
});